<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creator belongsTo Agency (nullable). Add engagement_rate, verification_status.
     */
    public function up(): void
    {
        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->after('user_id')->constrained('agencies')->nullOnDelete();
            $table->decimal('engagement_rate', 5, 2)->nullable()->after('min_rate');
            $table->string('verification_status')->default('unverified')->after('engagement_rate'); // unverified, pending, verified
        });

        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->index('agency_id');
            $table->index('verification_status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->dropForeign(['agency_id']);
            $table->dropColumn(['agency_id', 'engagement_rate', 'verification_status']);
            $table->dropIndex(['agency_id']);
            $table->dropIndex(['verification_status']);
            $table->dropIndex(['category']);
        });
    }
};
