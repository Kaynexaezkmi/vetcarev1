<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use App\Mail\AppointmentStatusNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class AppointmentController extends Controller
{
    public function create()
    {
        $services = Service::where('is_active', true)->get();
        return view('appointments.create', compact('services'));
    }

    public function store(Request $request)
    {
        $appointmentTime = $this->normalizeAppointmentTime($request->appointment_time);

        $validator = Validator::make($request->all(), [
            'pet_name' => 'required|string|max:255',
            'pet_type' => 'required|in:Dog,Cat,Bird,Rabbit,Hamster,Fish,Reptile,Other',
            'pet_breed' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'reason' => 'nullable|string|required_without:service_id',
        ], [
            'reason.required_without' => 'Please specify your reason for visit.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $exists = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $appointmentTime)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This time slot is already booked. Please select another time.');
        }

        $pet = Pet::create([
            'user_id' => Auth::id(),
            'name' => $request->pet_name,
            'type' => $request->pet_type,
            'breed' => $request->pet_breed,
        ]);

        try {
            Appointment::create([
                'user_id' => Auth::id(),
                'pet_id' => $pet->id,
                'service_id' => $request->service_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $appointmentTime,
                'reason' => $this->resolveAppointmentReason($request),
                'status' => 'pending',
            ]);
        } catch (QueryException $e) {
            if ($this->isAppointmentSlotConflict($e)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This date and time is already booked. Please select another time.');
            }

            throw $e;
        }

        return redirect()->route('dashboard')->with('success', 'Appointment booked successfully! We will review and confirm your appointment.');
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
        
        if ($appointment->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        if (!$appointment->canReschedule($isAdmin)) {
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
                'notes' => $appointment->notes . "\nRescheduled from: " . Carbon::parse($appointment->appointment_date)->format('M d, Y') . ' ' . $appointment->appointment_time,
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
            'notes' => trim(($appointment->notes ? $appointment->notes . "\n" : '') . 'Cancellation reason: ' . $request->reason),
        ]);

        $this->sendAppointmentStatusNotification($appointment->fresh(['pet', 'user', 'service']), 'Cancelled');

        return redirect()->back()->with('success', 'Appointment cancelled and email notification sent.');
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
        
        if (!$date) {
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

    protected function sendAppointmentStatusNotification(Appointment $appointment, string $actionLabel): void
    {
        $recipientEmails = User::admins()
            ->pluck('email')
            ->push($appointment->user->email)
            ->filter()
            ->unique()
            ->values();

        foreach ($recipientEmails as $email) {
            Mail::to($email)->send(new AppointmentStatusNotification($appointment, $actionLabel));
        }
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
                return 'Service appointment: ' . $service->name;
            }
        }

        return 'General consultation';
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
