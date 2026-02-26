<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Default cancellation policy per studio (flexible, moderate, strict).
     */
    public function up(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->string('cancellation_policy', 20)->default('moderate')->after('price_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn('cancellation_policy');
        });
    }
};
