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
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->string('sub_category')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->dropColumn('sub_category');
        });
        Schema::dropIfExists('sub_categories');
    }
};
