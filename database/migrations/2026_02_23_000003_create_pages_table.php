<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('template', 50)->nullable()->default('default');
            $table->string('status', 20)->default('draft'); // draft, published
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['slug', 'state_id', 'city_id'], 'pages_slug_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
