<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Studio extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'category', // legacy string; prefer category_id
        'description',
        'address',
        'city',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'price_per_hour',
        'price_per_day',
        'cancellation_policy',
        'status',
        'is_featured',
        'rating_avg',
        'reviews_count',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'decimal:2',
            'price_per_day' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'rating_avg' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(StudioCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(StudioImage::class, 'studio_id')->orderBy('sort_order');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'studio_amenity')->withTimestamps();
    }

    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class, 'studio_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'studio_id');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Main/primary image URL (first image or primary flag).
     */
    public function getMainImageUrlAttribute(): ?string
    {
        $img = $this->images()->where('is_primary', true)->first()
            ?? $this->images()->orderBy('sort_order')->first();
        if (! $img || ! $img->image) {
            return null;
        }
        return '/storage/' . ltrim($img->image, '/');
    }
}
