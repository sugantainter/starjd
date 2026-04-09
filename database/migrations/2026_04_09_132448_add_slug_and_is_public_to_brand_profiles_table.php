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
        Schema::table('brand_profiles', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('user_id');
            $table->boolean('is_public')->default(true)->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_profiles', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_public']);
        });
    }
};
