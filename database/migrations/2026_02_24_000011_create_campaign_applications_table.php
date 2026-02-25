<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Campaign hasMany Applications; Creator applies to campaign.
     */
    public function up(): void
    {
        Schema::create('campaign_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_message')->nullable();
            $table->decimal('quoted_amount', 12, 2)->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected, withdrawn
            $table->text('brand_notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        Schema::table('campaign_applications', function (Blueprint $table) {
            $table->index('campaign_id');
            $table->index('creator_id');
            $table->index('status');
            $table->unique(['campaign_id', 'creator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_applications');
    }
};
