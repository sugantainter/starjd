<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_settings', function (Blueprint $table) {
            $table->id();
            $table->string('headline')->default('Influencer Marketing Made Easy');
            $table->string('subheadline')->nullable();
            $table->json('wallpaper_images')->nullable(); // [{"src":"url"}, ...]
            $table->json('cascade_images')->nullable();   // [{"src":"url","alt":"..."}, ...]
            $table->string('btn_creator_label')->default('Join as Creator');
            $table->string('btn_creator_url')->default('#join-creator');
            $table->string('btn_brand_label')->default('Join as Brand');
            $table->string('btn_brand_url')->default('#join-brand');
            $table->string('btn_browse_label')->default('Browse Creators');
            $table->string('btn_browse_url')->default('#featured');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_settings');
    }
};
