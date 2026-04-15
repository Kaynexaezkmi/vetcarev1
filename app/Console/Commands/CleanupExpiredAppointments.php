<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupExpiredAppointments extends Command
{
    protected $signature = 'appointments:cleanup';
    protected $description = 'Delete pending appointments more than 24 hours past their scheduled date and time';

    public function handle()
    {
        $cutoff = Carbon::now()->subDay();
        
        $expiredAppointments = Appointment::where('status', 'pending')
            ->whereRaw('TIMESTAMP(appointment_date, appointment_time) <= ?', [$cutoff->format('Y-m-d H:i:s')])
            ->get();

        $count = $expiredAppointments->count();

        foreach ($expiredAppointments as $appointment) {
            $appointment->delete();
        }

        $this->info("Cleaned up {$count} expired pending appointments.");
    }
}
