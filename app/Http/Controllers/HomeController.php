<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Feedback;
use App\Models\Inquiry;
use App\Models\Service;
use App\Support\ServiceCatalog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        $feedback = Feedback::with('user', 'replies')->parentFeedback()->orderBy('created_at', 'desc')->limit(6)->get();

        return view('home.index', compact('services', 'feedback'));
    }

    public function services()
    {
        $services = Service::where('is_active', true)->get();
        $serviceCatalog = ServiceCatalog::forServices($services);

        return view('home.services', compact('services', 'serviceCatalog'));
    }

    public function about()
    {
        return view('home.about');
    }

    public function inquiryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|regex:/^[0-9]{11}$/',
            'message' => 'required|string',
        ]);

        $phone = null;
        if ($request->phone) {
            $phone = '+63'.$request->phone;
        }

        Inquiry::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $phone,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Thank you for your inquiry! We will get back to you soon.');
    }

    public function apiServices()
    {
        $services = Service::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function getAvailableSlots(Request $request)
    {
        $date = $request->get('date');
        $serviceId = $request->get('service_id');
        $excludeAppointmentId = $request->integer('exclude_appointment_id');

        $service = Service::find($serviceId);

        $isGrooming = false;
        $isDewormingOrVaccination = false;
        if ($service && strtolower($service->name) === 'grooming') {
            $isGrooming = true;
        }
        if ($service && (strtolower($service->name) === 'deworming' || strtolower($service->name) === 'vaccination')) {
            $isDewormingOrVaccination = true;
        }

        $timezone = 'Asia/Manila';
        $now = Carbon::now($timezone);
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');

        $bookedAppointments = Appointment::where('appointment_date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->when($excludeAppointmentId > 0, function ($query) use ($excludeAppointmentId) {
                $query->where('id', '!=', $excludeAppointmentId);
            })
            ->get();

        $bookedSlots = $bookedAppointments->map(function ($apt) {
            return Carbon::parse($apt->appointment_time)->format('H:i');
        })->toArray();

        $slots = [];

        if ($isGrooming) {
            $start = Carbon::createFromTime(8, 0, 0, $timezone);
            $end = Carbon::createFromTime(20, 0, 0, $timezone);
        } elseif ($isDewormingOrVaccination) {
            $start = Carbon::createFromTime(8, 0, 0, $timezone);
            $end = Carbon::createFromTime(20, 0, 0, $timezone);
        } else {
            $start = Carbon::createFromTime(0, 0, 0, $timezone);
            $end = Carbon::createFromTime(23, 30, 0, $timezone);
        }

        while ($start <= $end) {
            $timeString = $start->format('H:i');
            $isBooked = in_array($timeString, $bookedSlots);
            $isPast = $date === $today && $timeString <= $currentTime;
            $isAvailable = ! $isBooked && ! $isPast;

            $slots[] = [
                'time' => $timeString,
                'display' => $start->format('h:i A'),
                'available' => $isAvailable,
                'booked' => $isBooked,
                'past' => $isPast,
            ];

            $start->addMinutes(30);
        }

        return response()->json([
            'success' => true,
            'data' => $slots,
            'timezone' => 'Asia/Manila (GMT+8)',
            'current_time' => $now->format('Y-m-d H:i:s'),
        ]);
    }

    public function calendarEvents(Request $request)
    {
        $start = $request->get('start', now()->startOfMonth()->toDateString());
        $end = $request->get('end', now()->endOfMonth()->addMonths(2)->toDateString());

        $query = Appointment::whereBetween('appointment_date', [$start, $end])
            ->whereIn('status', ['pending', 'approved', 'completed', 'cancelled', 'rejected']);

        if (Auth::check() && ! Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $appointments = $query->get();

        $grouped = $appointments->groupBy(function ($apt) {
            return Carbon::parse($apt->appointment_date)->setTimezone('UTC')->format('Y-m-d');
        });

        $events = $grouped->map(function ($dayAppointments, $date) {
            return [
                'date' => $date,
                'count' => $dayAppointments->count(),
                'pending' => $dayAppointments->where('status', 'pending')->count(),
                'approved' => $dayAppointments->where('status', 'approved')->count(),
                'completed' => $dayAppointments->where('status', 'completed')->count(),
                'cancelled' => $dayAppointments->where('status', 'cancelled')->count(),
                'rejected' => $dayAppointments->where('status', 'rejected')->count(),
            ];
        })->values();

        return response()->json($events);
    }
}
