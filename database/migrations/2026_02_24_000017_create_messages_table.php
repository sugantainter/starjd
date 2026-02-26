<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Messaging: sender_id, receiver_id; supports campaign/booking threads via meta.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->string('thread_type')->nullable(); // campaign, booking, general
            $table->unsignedBigInteger('thread_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index(['thread_type', 'thread_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
