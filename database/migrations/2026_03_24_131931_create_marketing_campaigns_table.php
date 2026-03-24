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
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('type')->default('both'); // email, push, both
            $table->string('target_type')->default('all'); // all, individual, role, category
            $table->unsignedBigInteger('target_id')->nullable(); // role_id, user_id, or category_id
            $table->string('status')->default('draft'); // draft, queued, sending, completed, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};
