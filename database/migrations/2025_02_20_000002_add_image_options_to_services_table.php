<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('image_fit', 20)->nullable()->after('banner_image'); // cover, contain
            $table->string('banner_position', 20)->nullable()->after('image_fit'); // center, top, bottom
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['image_fit', 'banner_position']);
        });
    }
};
