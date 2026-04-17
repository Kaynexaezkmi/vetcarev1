<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Appointment::with(['pet', 'service', 'user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        } elseif ($request->has('date')) {
            $query->where('appointment_date', $request->date);
        } elseif ($request->has('month')) {
            $date = Carbon::parse($request->month);
            $query->whereBetween('appointment_date', [
                $date->startOfMonth()->toDateString(),
                $date->endOfMonth()->toDateString()
            ]);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
                              ->orderBy('appointment_time', 'desc')
                              ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $appointments,
        ]);
    }

    public function store(Request $request): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $exists = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $appointmentTime)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is already booked. Please select another time.',
            ], 409);
        }

        $pet = Pet::create([
            'user_id' => Auth::id(),
            'name' => $request->pet_name,
            'type' => $request->pet_type,
            'breed' => $request->pet_breed,
        ]);

        try {
            $appointment = Appointment::create([
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
                return response()->json([
                    'success' => false,
                    'message' => 'This date and time is already booked. Please select another time.',
                ], 409);
            }

            throw $e;
        }

        $appointment->load(['pet', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully!',
            'data' => $appointment,
        ], 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $appointment->load(['pet', 'service', 'user']);

        return response()->json([
            'success' => true,
            'data' => $appointment,
        ]);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'nullable|exists:services,id',
            'appointment_date' => 'nullable|date|after_or_equal:today',
            'appointment_time' => 'nullable',
            'reason' => 'nullable|string',
            'status' => 'nullable|in:pending,approved,completed,cancelled,rejected',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $appointment->update($request->only([
            'service_id',
            'appointment_date',
            'appointment_time',
            'reason',
            'status',
            'notes',
        ]));

        $appointment->load(['pet', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully!',
            'data' => $appointment,
        ]);
    }

    public function reschedule(Request $request, Appointment $appointment): JsonResponse
    {
        $appointmentTime = $this->normalizeAppointmentTime($request->appointment_time);

        if ($appointment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!$appointment->canReschedule()) {
            return response()->json([
                'success' => false,
                'message' => 'This appointment cannot be rescheduled.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $exists = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $appointmentTime)
            ->where('id', '!=', $appointment->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is already booked.',
            ], 409);
        }

        try {
            $appointment->update([
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $appointmentTime,
                'status' => 'pending',
                'rescheduled' => true,
                'notes' => $appointment->notes . "\nRescheduled from: " . 
                           Carbon::parse($appointment->appointment_date)->format('M d, Y') . ' ' . 
                           $appointment->appointment_time,
            ]);
        } catch (QueryException $e) {
            if ($this->isAppointmentSlotConflict($e)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This date and time is already booked. Please select another time.',
                ], 409);
            }

            throw $e;
        }

        $appointment->load(['pet', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully!',
            'data' => $appointment,
        ]);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        if ($appointment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_by' => Auth::user()->isAdmin() ? 'admin' : 'owner',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully!',
        ]);
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'service_id' => 'nullable',
        ]);

        $date = $request->get('date');
        $serviceId = $request->get('service_id');

        $service = null;
        $isGrooming = false;
        $isDewormingOrVaccination = false;
        
        if ($serviceId) {
            $service = Service::find($serviceId);
            if ($service && strtolower($service->name) === 'grooming') {
                $isGrooming = true;
            }
            if ($service && (strtolower($service->name) === 'deworming' || strtolower($service->name) === 'vaccination')) {
                $isDewormingOrVaccination = true;
            }
        }

        $bookedSlots = Appointment::bookedSlots($date);

        $slots = [];
        
        if ($isGrooming) {
            $start = Carbon::createFromTime(8, 0, 0);
            $end = Carbon::createFromTime(19, 0, 0);
        } elseif ($isDewormingOrVaccination) {
            $start = Carbon::createFromTime(8, 0, 0);
            $end = Carbon::createFromTime(20, 0, 0);
        } else {
            $start = Carbon::createFromTime(0, 0, 0);
            $end = Carbon::createFromTime(23, 30, 0);
        }

        while ($start <= $end) {
            $timeString = $start->format('H:i');
            $isAvailable = !in_array($timeString, $bookedSlots);
            $isPast = $date === now()->toDateString() && $start->format('H:i') < now()->format('H:i');

            $slots[] = [
                'time' => $timeString,
                'display' => $start->format('h:i A'),
                'available' => $isAvailable && !$isPast,
                'booked' => !$isAvailable,
                'past' => $isPast,
            ];

            $start->addMinutes(30);
        }

        return response()->json([
            'success' => true,
            'data' => $slots,
        ]);
    }

    public function calendarEvents(Request $request): JsonResponse
    {
        $start = $request->get('start', now()->startOfMonth()->toDateString());
        $end = $request->get('end', now()->endOfMonth()->addMonth(2)->toDateString());

        $query = Appointment::with(['pet', 'service']);

        if ($request->has('user_id') && !$request->has('all')) {
            $query->where('user_id', $request->user_id);
        }

        $appointments = $query->whereBetween('appointment_date', [$start, $end])->get();

        $events = $appointments->map(function ($apt) {
            $color = match($apt->status) {
                'approved' => '#10b981',
                'pending' => '#f59e0b',
                'completed' => '#3b82f6',
                default => '#6b7280',
            };

            $dateStr = Carbon::parse($apt->appointment_date)->setTimezone('UTC')->format('Y-m-d');

            return [
                'id' => $apt->id,
                'title' => $apt->pet->name . ' - ' . ($apt->service ? $apt->service->name : 'Checkup'),
                'start' => $dateStr . 'T' . $apt->appointment_time,
                'end' => $dateStr . 'T' . Carbon::parse($apt->appointment_time)->addMinutes(30)->format('H:i'),
                'color' => $color,
                'status' => $apt->status,
                'extendedProps' => [
                    'pet' => $apt->pet->name,
                    'service' => $apt->service ? $apt->service->name : 'Checkup',
                    'user' => $apt->user->name ?? null,
                    'reason' => $apt->reason,
                    'status' => $apt->status,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    public function approve(Appointment $appointment): JsonResponse
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $appointment->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment approved!',
            'data' => $appointment,
        ]);
    }

    public function reject(Appointment $appointment): JsonResponse
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $appointment->update([
            'status' => 'rejected',
            'cancelled_by' => 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rejected!',
            'data' => $appointment,
        ]);
    }

    public function complete(Appointment $appointment): JsonResponse
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $appointment->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment marked as completed!',
            'data' => $appointment,
        ]);
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
