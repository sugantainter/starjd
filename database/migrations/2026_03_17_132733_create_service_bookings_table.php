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
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_listing_id')->constrained()->onDelete('cascade');
            $table->string('package_type')->default('basic'); // basic, standard, premium
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending'); // pending, in_progress, delivered, completed, cancelled, revision_requested
            $table->text('requirements')->nullable();
            $table->json('delivery_files')->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamp('due_at')->nullable();
            $table->integer('revisions_remaining')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
