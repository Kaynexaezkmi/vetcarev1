<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('notes');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('payment_proof_path')->nullable()->after('payment_reference');
            $table->decimal('service_amount', 10, 2)->default(0)->after('payment_proof_path');
            $table->decimal('reservation_fee', 10, 2)->default(0)->after('service_amount');
            $table->timestamp('payment_submitted_at')->nullable()->after('reservation_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_reference',
                'payment_proof_path',
                'service_amount',
                'reservation_fee',
                'payment_submitted_at',
            ]);
        });
    }
};
