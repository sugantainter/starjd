<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('brand_notes')->constrained()->nullOnDelete();
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('customer_notes')->constrained()->nullOnDelete();
        });
        Schema::table('access_payments', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('gateway_ref')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
        });
        Schema::table('access_payments', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
        });
    }
};
