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
        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic link to raw data (Area, Hospital, etc.)
            $table->string('entity_type')->nullable(); // App\Models\Hospital, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            
            // Content Type (e.g., 'hospital', 'service', 'market')
            $table->string('type')->index(); 
            
            // Core SEO
            $table->string('slug')->unique();
            $table->string('title'); // H1
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Sulekha-style Content
            $table->text('intro_text')->nullable(); // SEO Paragraph
            $table->json('guide_content')->nullable(); // Array of sections with titles/content
            $table->json('faqs')->nullable(); // Array of {q, a}
            
            // Settings
            $table->string('status')->default('draft'); // published, draft
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_pages');
    }
};
