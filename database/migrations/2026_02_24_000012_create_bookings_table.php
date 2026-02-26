<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Booking belongsTo User (customer), belongsTo Studio.
     * Prevent double booking via unique constraint / app logic.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // customer
            $table->foreignId('studio_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, completed
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('customer_notes')->nullable();
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('studio_id');
            $table->index('status');
            $table->index(['studio_id', 'date']);
            $table->index('date');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
