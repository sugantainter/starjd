<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->boolean('is_short')->default(false)->after('sort_order');
        });

        // Mark first 3 videos as shorts so Brand page shows YouTube content
        if (Schema::hasTable('videos')) {
            $ids = \Illuminate\Support\Facades\DB::table('videos')
                ->orderBy('sort_order')
                ->limit(3)
                ->pluck('id');
            if ($ids->isNotEmpty()) {
                \Illuminate\Support\Facades\DB::table('videos')
                    ->whereIn('id', $ids)
                    ->update(['is_short' => true]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('is_short');
        });
    }
};
