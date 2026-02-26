<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot: Studio has many Amenities.
     */
    public function up(): void
    {
        Schema::create('studio_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['studio_id', 'amenity_id']);
        });

        Schema::table('studio_amenity', function (Blueprint $table) {
            $table->index('studio_id');
            $table->index('amenity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studio_amenity');
    }
};
