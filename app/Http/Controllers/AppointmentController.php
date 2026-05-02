<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentStatusNotification;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use App\Support\ServiceCatalog;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class AppointmentController extends Controller
{
    public function create(): RedirectResponse|View
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.appointments.index')
                ->with('error', 'Admins cannot book customer appointments.');
        }

        $pets = Auth::user()->pets()->orderBy('name')->get();

        if ($pets->isEmpty()) {
            return redirect(route('settings').'#pet-profile')
                ->with('error', 'Please add a pet profile in Settings before booking an appointment.');
        }

        $services = Service::where('is_active', true)->get();
        $serviceCatalog = ServiceCatalog::forServices($services);
        $submittedAppointment = null;

        if (session()->has('submitted_appointment_id')) {
            $submittedAppointment = Appointment::with(['pet', 'service'])
                ->where('user_id', Auth::id())
                ->find(session('submitted_appointment_id'));
        }

        return view('appointments.create', compact('services', 'serviceCatalog', 'pets', 'submittedAppointment'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.appointments.index')
                ->with('error', 'Admins cannot book customer appointments.');
        }

        if (! Auth::user()->pets()->exists()) {
            return redirect(route('settings').'#pet-profile')
                ->with('error', 'Please add a pet profile in Settings before booking an appointment.');
        }

        $appointmentTime = $this->normalizeAppointmentTime($request->appointment_time);

        $validator = Validator::make($request->all(), [
            'pet_id' => [
                'required',
                'integer',
                Rule::exists('pets', 'id')->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'service_id' => 'nullable|exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'reason' => 'nullable|string|required_without:service_id',
            'appointment_notes' => 'nullable|string|max:500',
            'payment_proof' => 'nullable|required_without:payment_reference|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_reference' => 'nullable|required_without:payment_proof|string|max:50',
            'terms_agreement' => 'accepted',
        ], [
            'reason.required_without' => 'Please specify your reason for visit.',
            'payment_proof.required_without' => 'Please upload a payment image or enter a GCash reference number.',
            'payment_reference.required_without' => 'Please upload a payment image or enter a GCash reference number.',
            'terms_agreement.accepted' => 'Please agree to the Terms and Conditions before confirming your appointment.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pet = Pet::where('user_id', Auth::id())->findOrFail((int) $request->pet_id);

        $exists = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $appointmentTime)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This time slot is already booked. Please select another time.');
        }

        $service = $request->filled('service_id')
            ? Service::find((int) $request->service_id)
            : null;
        $serviceAmount = $this->resolveServiceAmount($service);
        $paymentProofPath = null;

        try {
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
            }

            $appointment = Appointment::create([
                'user_id' => Auth::id(),
                'pet_id' => $pet->id,
                'service_id' => $request->service_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $appointmentTime,
                'reason' => $this->resolveAppointmentReason($request),
                'status' => 'pending',
                'notes' => $request->filled('appointment_notes') ? trim((string) $request->appointment_notes) : null,
                'payment_method' => 'GCash',
                'payment_reference' => $request->filled('payment_reference') ? trim((string) $request->payment_reference) : null,
                'payment_proof_path' => $paymentProofPath,
                'service_amount' => $serviceAmount,
                'reservation_fee' => $serviceAmount * 0.2,
                'payment_submitted_at' => now(),
            ]);
        } catch (QueryException $e) {
            if ($paymentProofPath) {
                Storage::disk('public')->delete($paymentProofPath);
            }

            if ($this->isAppointmentSlotConflict($e)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This date and time is already booked. Please select another time.');
            }

            throw $e;
        }

        return redirect()->route('appointments.create')
            ->with('submitted_appointment_id', $appointment->id)
            ->with('success', 'Appointment submitted successfully! We will review and confirm your appointment.');
    }

    public function history()
    {
        $appointments = Appointment::with(['pet', 'service'])
            ->where('user_id', Auth::id())
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10);

        return view('appointments.history', compact('appointments'));
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $isAdmin = Auth::user()->isAdmin();
        $appointmentTime = $request->filled('appointment_time')
            ? $this->normalizeAppointmentTime($request->appointment_time)
            : null;

        if ($appointment->user_id !== Auth::id() && ! $isAdmin) {
            abort(403);
        }

        if (! $appointment->canReschedule($isAdmin)) {
            return redirect()->back()->with('error', 'This appointment cannot be rescheduled.');
        }

        if ($request->isMethod('get')) {
            $services = Service::where('is_active', true)->get();

            return view('appointments.reschedule', compact('appointment', 'services'));
        }

        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $exists = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $appointmentTime)
            ->where('id', '!=', $appointment->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This time slot is already booked.');
        }

        try {
            $appointment->update([
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $appointmentTime,
                'status' => 'pending',
                'rescheduled' => true,
                'notes' => $appointment->notes."\nRescheduled from: ".Carbon::parse($appointment->appointment_date)->format('M d, Y').' '.$appointment->appointment_time,
            ]);
        } catch (QueryException $e) {
            if ($this->isAppointmentSlotConflict($e)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This date and time is already booked. Please select another time.');
            }

            throw $e;
        }

        if ($isAdmin) {
            return redirect()->route('admin.appointments.index')->with('success', 'Appointment rescheduled successfully!');
        }

        return redirect()->route('dashboard')->with('success', 'Appointment rescheduled successfully!');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->reason,
            'cancelled_by' => 'owner',
            'notes' => trim(($appointment->notes ? $appointment->notes."\n" : '').'Cancellation reason: '.$request->reason),
        ]);

        $notificationSent = $this->sendAppointmentStatusNotification($appointment->fresh(['pet', 'user', 'service']), 'Cancelled');

        return redirect()->back()->with(
            $notificationSent ? 'success' : 'error',
            $notificationSent
                ? 'Appointment cancelled and email notification sent.'
                : 'Appointment cancelled, but the email notification could not be sent. Please check the mail settings.'
        );
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($appointment->status !== 'cancelled') {
            return redirect()->back()->with('error', 'Only cancelled appointments can be deleted.');
        }

        $appointment->delete();

        return redirect()->route('appointments.history')->with('success', 'Cancelled appointment deleted successfully.');
    }

    public function getAppointmentsByDate(Request $request)
    {
        $date = $request->get('date');

        if (! $date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $isAdmin = Auth::user()->isAdmin();

        if ($isAdmin) {
            $appointments = Appointment::with(['pet', 'user', 'service'])
                ->where('appointment_date', $date)
                ->orderBy('appointment_time')
                ->get();
        } else {
            $appointments = Appointment::with(['pet', 'service'])
                ->where('appointment_date', $date)
                ->where('user_id', Auth::id())
                ->orderBy('appointment_time')
                ->get();
        }

        $data = $appointments->map(function ($apt) use ($isAdmin) {
            return [
                'id' => $apt->id,
                'time' => Carbon::parse($apt->appointment_time)->format('h:i A'),
                'pet_name' => $apt->pet->name,
                'service' => $apt->service ? $apt->service->name : 'Checkup',
                'status' => $apt->status,
                'status_label' => $apt->status_label,
                'user_name' => $isAdmin ? $apt->user->name : null,
                'reason' => $apt->reason,
                'cancellation_reason' => $apt->cancellation_reason,
            ];
        });

        return response()->json([
            'date' => $date,
            'appointments' => $data,
            'total' => $data->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'approved' => $appointments->where('status', 'approved')->count(),
        ]);
    }

    protected function sendAppointmentStatusNotification(Appointment $appointment, string $actionLabel): bool
    {
        $recipientEmails = User::admins()
            ->pluck('email')
            ->push($appointment->user->email)
            ->filter()
            ->unique()
            ->values();

        foreach ($recipientEmails as $email) {
            try {
                Mail::to($email)->send(new AppointmentStatusNotification($appointment, $actionLabel));
            } catch (Throwable $e) {
                Log::warning('Appointment status notification email failed.', [
                    'appointment_id' => $appointment->id,
                    'action' => $actionLabel,
                    'email' => $email,
                    'message' => $e->getMessage(),
                ]);

                return false;
            }
        }

        return true;
    }

    protected function resolveAppointmentReason(Request $request): string
    {
        $reason = trim((string) $request->reason);

        if ($reason !== '') {
            return $reason;
        }

        if ($request->filled('service_id')) {
            $service = Service::find($request->service_id);

            if ($service) {
                return 'Service appointment: '.$service->name;
            }
        }

        return 'General consultation';
    }

    protected function resolveServiceAmount(?Service $service): float
    {
        if (! $service) {
            return 0.0;
        }

        $catalogService = ServiceCatalog::forServices(collect([$service]))
            ->firstWhere('service_id', $service->id);

        return (float) ($catalogService['booking_amount'] ?? $service->price ?? 0);
    }

    protected function normalizeAppointmentTime(string $appointmentTime): string
    {
        return Carbon::parse($appointmentTime)->format('H:i:s');
    }

    protected function isAppointmentSlotConflict(QueryException $e): bool
    {
        return str_contains(strtolower($e->getMessage()), 'appointments_slot_guard_unique');
    }
}
