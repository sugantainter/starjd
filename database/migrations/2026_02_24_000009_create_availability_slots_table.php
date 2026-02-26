<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Studio availability calendar; used to prevent double booking.
     */
    public function up(): void
    {
        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);
            $table->string('recurrence')->nullable(); // null, daily, weekly
            $table->timestamps();
        });

        Schema::table('availability_slots', function (Blueprint $table) {
            $table->index('studio_id');
            $table->index(['studio_id', 'date']);
            $table->index('date');
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_slots');
    }
};
