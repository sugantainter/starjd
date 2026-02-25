<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Linked to bookings/campaigns; supports platform, agency, creator, studio amounts.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // booking, campaign, payout, refund, topup
            $table->decimal('amount', 14, 2);
            $table->decimal('platform_amount', 14, 2)->default(0);
            $table->decimal('agency_amount', 14, 2)->default(0);
            $table->decimal('creator_amount', 14, 2)->default(0);
            $table->decimal('studio_amount', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('gateway')->nullable(); // razorpay, stripe
            $table->string('gateway_ref')->nullable();
            $table->string('payable_type')->nullable(); // Booking, Campaign, etc.
            $table->unsignedBigInteger('payable_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // payer or recipient
            $table->text('meta')->nullable(); // JSON
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('wallet_id');
            $table->index('type');
            $table->index('payment_status');
            $table->index(['payable_type', 'payable_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
