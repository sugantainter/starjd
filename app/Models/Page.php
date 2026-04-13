<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'template',
        'status',
        'sort_order',
        'state_id',
        'city_id',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('state_id')->whereNull('city_id');
    }

    /**
     * Public URL path (single segment), matching the front-end router — e.g. /influencers-in-ahmedabad.
     */
    public function publicPath(): ?string
    {
        if (! filled($this->slug)) {
            return null;
        }

        if ($this->city_id && $this->city?->slug && $this->state?->slug) {
            // New structure: state/slug-in-city (e.g. /madhya-pradesh/influencers-in-bhopal)
            return '/'.Str::slug($this->state->slug).'/'.$this->slug.'-in-'.Str::slug($this->city->slug);
        }

        if ($this->state_id && $this->state?->slug) {
            // New state structure: /state (for influencers) or /state/slug
            if ($this->slug === 'influencers') {
                return '/'.Str::slug($this->state->slug);
            }
            return '/'.Str::slug($this->state->slug).'/'.$this->slug;
        }

        if ($this->city_id || $this->state_id) {
            return null;
        }

        return '/'.$this->slug;
    }
}
