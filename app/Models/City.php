<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class City extends Model
{
    protected $fillable = ['state_id', 'name', 'slug', 'sort_order'];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Resolve a city from a URL path segment (hyphenated canonical or legacy spaced slug).
     */
    public static function findByUrlSlug(?string $raw): ?self
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        $raw = trim(urldecode($raw));
        $norm = Str::slug($raw);

        $hit = static::query()
            ->where(function ($q) use ($raw, $norm) {
                $q->where('slug', $raw)->orWhere('slug', $norm);
            })
            ->first();

        if ($hit) {
            return $hit;
        }

        return static::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereRaw(
                'LOWER(REPLACE(REPLACE(TRIM(slug), ?, ?), ?, ?)) = ?',
                [' ', '-', '_', '-', Str::lower($norm)]
            )
            ->first();
    }
}
