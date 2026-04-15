<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Reminder;
use App\Models\Appointment;
use App\Models\Inquiry;
use App\Models\MedicalRecord;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('components.dashboard.header', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $unreadRemindersCount = Reminder::forUser($user->id)->unread()->count();
                $view->with('unreadRemindersCount', $unreadRemindersCount);
            }
        });

        View::composer('components.dashboard.sidebar', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                
                $unreadRemindersCount = Reminder::forUser($user->id)->unread()->count();
                
                if ($user->isAdmin()) {
                    $pendingAppointmentsCount = Appointment::where('status', 'pending')->count();
                    $unreadInquiriesCount = Inquiry::where('is_read', false)->count();
                    $newMedicalRecordsCount = MedicalRecord::where('created_at', '>=', now()->subDays(7))->count();
                    
                    $view->with([
                        'unreadRemindersCount' => $unreadRemindersCount,
                        'pendingAppointmentsCount' => $pendingAppointmentsCount,
                        'unreadInquiriesCount' => $unreadInquiriesCount,
                        'newMedicalRecordsCount' => $newMedicalRecordsCount,
                    ]);
                } else {
                    $recentMedicalRecordsCount = MedicalRecord::whereHas('pet', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->whereNull('seen_by_user_at')->count();
                    
                    $view->with([
                        'unreadRemindersCount' => $unreadRemindersCount,
                        'recentMedicalRecordsCount' => $recentMedicalRecordsCount,
                    ]);
                }
            }
        });
    }
}
