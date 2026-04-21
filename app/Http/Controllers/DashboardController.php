<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\MedicalRecord;
use App\Models\Inquiry;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }
        
        return $this->userDashboard();
    }

    protected function userDashboard()
    {
        $user = Auth::user();
        
        $now = Carbon::now();
        Appointment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where(function ($query) use ($now) {
                $query->where('appointment_date', '<', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('appointment_date', $now->toDateString())
                          ->where('appointment_time', '<', $now->format('H:i:s'));
                    });
            })
            ->delete();
        
        $upcomingAppointments = Appointment::with(['pet', 'service'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(5)
            ->get();

        $recentAppointments = Appointment::with(['pet', 'service'])
            ->where('user_id', $user->id)
            ->orderBy('appointment_date', 'desc')
            ->limit(10)
            ->get();

        $calendarEvents = Appointment::where('user_id', $user->id)
            ->where('appointment_date', '>=', now()->startOfMonth()->toDateString())
            ->where('appointment_date', '<=', now()->endOfMonth()->addMonths(2)->toDateString())
            ->get()
            ->map(function ($apt) {
                $color = match($apt->status) {
                    'approved' => '#10b981',
                    'pending' => '#f59e0b',
                    'completed' => '#3b82f6',
                    default => '#6b7280',
                };
                return [
                    'id' => $apt->id,
                    'title' => $apt->pet->name . ' - ' . ($apt->service ? $apt->service->name : 'Checkup'),
                    'start' => $apt->appointment_date->format('Y-m-d') . 'T' . $apt->appointment_time,
                    'color' => $color,
                    'status' => $apt->status,
                ];
            });

        return view('dashboard.user', compact('upcomingAppointments', 'recentAppointments', 'calendarEvents'));
    }

    protected function adminDashboard()
    {
        $now = Carbon::now();
        Appointment::where('status', 'pending')
            ->where(function ($query) use ($now) {
                $query->where('appointment_date', '<', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('appointment_date', $now->toDateString())
                          ->where('appointment_time', '<', $now->format('H:i:s'));
                    });
            })
            ->delete();

        $todayAppointments = Appointment::with(['pet', 'user', 'service'])
            ->where('appointment_date', now()->toDateString())
            ->orderBy('appointment_time')
            ->get();

        $pendingAppointments = Appointment::with(['pet', 'user', 'service'])
            ->where('status', 'pending')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(10)
            ->get();

        $recentAppointments = Appointment::with(['pet', 'user', 'service'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'today' => Appointment::where('appointment_date', now()->toDateString())->count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'total_patients' => Pet::count(),
            'total_services' => Service::count(),
            'unread_inquiries' => Inquiry::where('is_read', false)->count(),
        ];

        $calendarEvents = Appointment::with(['pet', 'user', 'service'])
            ->where('appointment_date', '>=', now()->startOfMonth()->toDateString())
            ->where('appointment_date', '<=', now()->endOfMonth()->addMonths(2)->toDateString())
            ->get()
            ->map(function ($apt) {
                $color = match($apt->status) {
                    'approved' => '#10b981',
                    'pending' => '#f59e0b',
                    'completed' => '#3b82f6',
                    default => '#6b7280',
                };
                return [
                    'id' => $apt->id,
                    'title' => $apt->pet->name . ' (' . $apt->user->name . ')',
                    'start' => $apt->appointment_date->format('Y-m-d') . 'T' . $apt->appointment_time,
                    'color' => $color,
                    'status' => $apt->status,
                ];
            });

        return view('dashboard.admin', compact('todayAppointments', 'pendingAppointments', 'recentAppointments', 'stats', 'calendarEvents'));
    }

    public function reminders()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $reminders = Reminder::with(['appointment.pet', 'appointment.user'])
                ->whereHas('appointment', function ($q) {
                    $q->where('appointment_date', '>=', now()->toDateString());
                })
                ->orderBy('send_at')
                ->paginate(15);
            
            Reminder::whereNull('read_at')
                ->whereHas('appointment', function ($q) {
                    $q->where('appointment_date', '>=', now()->toDateString());
                })
                ->update(['read_at' => now()]);
        } else {
            $reminders = Reminder::with(['appointment.pet', 'appointment.service'])
                ->where('user_id', $user->id)
                ->orderBy('send_at')
                ->paginate(15);
            
            Reminder::where('user_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('dashboard.reminders', compact('reminders'));
    }

    public function settings()
    {
        $pets = Auth::user()->pets()->orderBy('name')->get();

        return view('dashboard.settings', compact('pets'));
    }

    public function deleteReminder(Reminder $reminder)
    {
        if (Auth::user()->isAdmin() || $reminder->user_id === Auth::id()) {
            $reminder->delete();
            return redirect()->back()->with('success', 'Reminder deleted successfully!');
        }
        abort(403);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|regex:/^[0-9]{11}$/',
            'address' => 'nullable|string',
        ]);

        $phone = null;
        if ($request->phone) {
            $phone = '+63' . $request->phone;
        }

        Auth::user()->update([
            'phone' => $phone,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
