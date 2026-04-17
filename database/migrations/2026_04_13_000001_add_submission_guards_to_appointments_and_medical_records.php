<?php

use App\Models\Appointment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('slot_guard')->nullable()->after('appointment_time');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('submission_token')->nullable()->after('created_by');
        });

        $usedSlotGuards = [];

        Appointment::query()
            ->orderBy('id')
            ->get()
            ->each(function (Appointment $appointment) use (&$usedSlotGuards) {
                $slotGuard = null;

                if ($appointment->shouldBlockSlot()) {
                    $slotGuard = $appointment->buildSlotGuard();

                    if (isset($usedSlotGuards[$slotGuard])) {
                        $slotGuard = $slotGuard . '#legacy-' . $appointment->id;
                    } else {
                        $usedSlotGuards[$slotGuard] = true;
                    }
                }

                DB::table('appointments')
                    ->where('id', $appointment->id)
                    ->update(['slot_guard' => $slotGuard]);
            });

        DB::table('medical_records')
            ->whereNull('submission_token')
            ->orderBy('id')
            ->get()
            ->each(function ($record) {
                DB::table('medical_records')
                    ->where('id', $record->id)
                    ->update(['submission_token' => (string) Str::uuid()]);
            });

        Schema::table('appointments', function (Blueprint $table) {
            $table->unique('slot_guard', 'appointments_slot_guard_unique');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->unique('submission_token', 'medical_records_submission_token_unique');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('appointments_slot_guard_unique');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropUnique('medical_records_submission_token_unique');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('slot_guard');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('submission_token');
        });
    }
};
