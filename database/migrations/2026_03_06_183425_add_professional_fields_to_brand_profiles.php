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
            $table->string('industry')->nullable()->after('bio');
            $table->string('hq_location')->nullable()->after('industry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_profiles', function (Blueprint $table) {
            $table->dropColumn(['industry', 'hq_location']);
        });
    }
};
