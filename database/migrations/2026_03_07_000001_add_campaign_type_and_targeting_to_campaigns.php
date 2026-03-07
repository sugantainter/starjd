<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * campaign_type: instagram, tiktok, ugc, youtube
     * targeting: JSON { niches[], follower_ranges[], countries[], cities[], genders[], ages[], ethnicities[], languages[] }
     */
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('campaign_type')->nullable()->after('brand_id');
            $table->json('targeting')->nullable()->after('max_applications');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['campaign_type', 'targeting']);
        });
    }
};
