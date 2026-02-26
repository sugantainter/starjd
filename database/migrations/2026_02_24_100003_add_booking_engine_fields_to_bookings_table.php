<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Booking statuses: pending, payment_pending, confirmed, completed, cancelled, refunded.
     * Cancellation policy: flexible, moderate, strict.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('cancellation_policy')->default('moderate')->after('customer_notes'); // flexible, moderate, strict
            $table->string('payment_status')->default('pending')->after('amount'); // pending, paid, failed, refunded
            $table->string('gateway_ref')->nullable()->after('payment_status');
            $table->decimal('platform_commission', 12, 2)->default(0)->after('amount');
            $table->decimal('studio_amount', 12, 2)->nullable()->after('platform_commission');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['cancellation_policy', 'payment_status', 'gateway_ref', 'platform_commission', 'studio_amount']);
            $table->dropIndex(['payment_status']);
        });
    }
};
