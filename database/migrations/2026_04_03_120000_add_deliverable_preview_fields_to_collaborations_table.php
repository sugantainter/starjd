<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->string('deliverable_preview_path')->nullable()->after('deliverable_content');
            $table->string('deliverable_preview_status', 32)->default('ready')->after('deliverable_preview_path');
        });
    }

    public function down(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->dropColumn(['deliverable_preview_path', 'deliverable_preview_status']);
        });
    }
};
