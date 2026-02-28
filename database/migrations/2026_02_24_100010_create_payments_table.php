<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32); // access, collaboration, booking
            $table->string('payable_type', 64);
            $table->unsignedBigInteger('payable_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('status', 24)->default('pending'); // pending, completed, failed
            $table->string('gateway', 24)->default('payu');
            $table->string('txnid', 64)->unique();
            $table->string('gateway_ref', 128)->nullable();
            $table->json('request_params')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['type', 'payable_type', 'payable_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
