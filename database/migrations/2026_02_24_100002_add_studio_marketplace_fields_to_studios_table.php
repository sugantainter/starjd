<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add category_id (FK), latitude, longitude, rating_avg, reviews_count (cached).
     * slug, city, state already exist on studios.
     */
    public function up(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained('studio_categories')->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable()->after('state');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->decimal('rating_avg', 3, 2)->nullable()->after('is_featured');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating_avg');
        });

        Schema::table('studios', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('city');
            $table->index(['latitude', 'longitude']);
            $table->index('rating_avg');
            $table->index('reviews_count');
        });
    }

    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'latitude', 'longitude', 'rating_avg', 'reviews_count']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['city']);
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['rating_avg']);
            $table->dropIndex(['reviews_count']);
        });
    }
};
