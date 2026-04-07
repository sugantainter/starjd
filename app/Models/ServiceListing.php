<?php

namespace App\Models;

use App\Support\StoragePublicUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceListing extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'title',
        'slug',
        'description',
        'pricing_tiers',
        'gallery',
        'faqs',
        'metadata',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'pricing_tiers' => 'array',
        'gallery' => 'array',
        'faqs' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    /**
     * Browser-ready URLs for gallery items (DB stores object keys or legacy URLs).
     *
     * @return list<string>
     */
    public function resolvedGalleryUrls(): array
    {
        $paths = $this->gallery;
        if (! is_array($paths)) {
            return [];
        }

        $out = [];
        foreach ($paths as $item) {
            if ($item === null || $item === '' || ! is_string($item)) {
                continue;
            }
            $item = trim($item);
            if ($item === '') {
                continue;
            }
            $url = StoragePublicUrl::resolve($item);
            if ($url !== null && $url !== '') {
                $out[] = $url;
            }
        }

        return $out;
    }
}
