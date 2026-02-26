<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Brand hasMany Campaigns; define budget, deliverables, invite creators.
     */
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->text('deliverables')->nullable();
            $table->decimal('budget', 12, 2);
            $table->string('status')->default('draft'); // draft, open, in_progress, completed, cancelled
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('max_applications')->nullable();
            $table->timestamps();
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->index('brand_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
