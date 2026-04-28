<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class SeoPage extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'type',
        'slug',
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'intro_text',
        'guide_content',
        'faqs',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'guide_content' => 'array',
        'faqs' => 'array',
        'is_featured' => 'boolean',
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Generate unique slug for the SEO page.
     */
    public static function generateUniqueSlug(string $name, string $city, string $type): string
    {
        $base = Str::slug($name . ' in ' . $city . ' ' . $type);
        $slug = $base;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
