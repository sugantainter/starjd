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
        Schema::table('collaborations', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable();
            $table->string('deliverable_type')->nullable(); // 'link' or 'file'
            $table->text('deliverable_content')->nullable(); // the link or the file path
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'deliverable_type', 'deliverable_content', 'delivered_at', 'completed_at']);
        });
    }
};
