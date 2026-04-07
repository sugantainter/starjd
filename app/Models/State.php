<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class State extends Model
{
    protected $fillable = ['country_id', 'name', 'slug', 'code', 'sort_order'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Resolve a state from a URL path segment (may be hyphenated or legacy "with spaces").
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

        return static::query()->get()->first(function (self $s) use ($norm) {
            return Str::slug((string) $s->slug) === $norm;
        });
    }
}
