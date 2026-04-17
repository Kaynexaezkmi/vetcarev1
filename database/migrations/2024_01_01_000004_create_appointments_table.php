<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('reason');
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->boolean('rescheduled')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['appointment_date', 'appointment_time']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
