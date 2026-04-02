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
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('revisions')->default(0)->after('deliverables');
        });

        Schema::table('collaborations', function (Blueprint $table) {
            $table->integer('revision_count')->default(0)->after('brand_notes');
            $table->text('revision_notes')->nullable()->after('revision_count');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('revisions');
        });

        Schema::table('collaborations', function (Blueprint $table) {
            $table->dropColumn(['revision_count', 'revision_notes']);
        });
    }
};
