<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Studio marketplace: StudioOwner hasMany Studios.
     * Categories: Photography, Film, Podcast, Music, Villa, Event Space.
     */
    public function up(): void
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // studio_owner
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->string('category'); // photography, film, podcast, music, villa, event_space
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 20)->nullable();
            $table->decimal('price_per_hour', 12, 2)->nullable();
            $table->decimal('price_per_day', 12, 2)->nullable();
            $table->string('status')->default('draft'); // draft, active, inactive, suspended
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::table('studios', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('category');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};
