<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Mail\AppointmentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send pending appointment reminders';

    public function handle(): int
    {
        $reminders = Reminder::where('is_sent', false)
            ->where('send_at', '<=', now())
            ->get();

        foreach ($reminders as $reminder) {
            try {
                Mail::to($reminder->user->email)->send(new AppointmentReminder($reminder->appointment));
                
                $reminder->update([
                    'is_sent' => true,
                    'sent_at' => now(),
                ]);
                
                $this->info("Sent reminder for appointment ID: {$reminder->appointment_id}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for appointment ID: {$reminder->appointment_id} - " . $e->getMessage());
            }
        }

        $this->info('Reminder sending completed.');
        return self::SUCCESS;
    }
}