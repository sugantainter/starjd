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
        Schema::table('service_listings', function (Blueprint $table) {
            $table->json('faqs')->nullable();
            $table->json('metadata')->nullable(); // For industry, platform, etc.
            $table->json('tags')->nullable();
            $table->json('gallery')->nullable()->change(); // Ensure it's json (already was probably, but being sure)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_listings', function (Blueprint $table) {
            $table->dropColumn(['faqs', 'metadata', 'tags']);
        });
    }
};
