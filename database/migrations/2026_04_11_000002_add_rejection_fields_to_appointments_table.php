<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments')) {
            return;
        }

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE appointments MODIFY status ENUM('pending','approved','completed','cancelled','rejected') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('rescheduled');
            }

            if (! Schema::hasColumn('appointments', 'cancelled_by')) {
                $table->string('cancelled_by')->nullable()->after('cancellation_reason');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'cancelled_by')) {
                $table->dropColumn('cancelled_by');
            }

            if (Schema::hasColumn('appointments', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE appointments MODIFY status ENUM('pending','approved','completed','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
