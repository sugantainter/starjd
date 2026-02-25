<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * In-app notifications; Laravel notification channels can use this.
     */
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (! $this->indexExists('notifications', 'notifications_read_at_index')) {
                $table->index('read_at');
            }
        });
    }

    private function indexExists(string $table, string $name): bool
    {
        $indexes = Schema::getIndexListing($table);
        return in_array($name, $indexes, true);
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
