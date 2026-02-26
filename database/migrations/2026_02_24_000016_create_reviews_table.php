<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Polymorphic: reviewable = CreatorProfile, Studio, etc.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reviewable_type'); // App\Models\CreatorProfile, App\Models\Studio
            $table->unsignedBigInteger('reviewable_id');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->string('status')->default('approved'); // pending, approved, rejected
            $table->timestamps();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index('rating');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
