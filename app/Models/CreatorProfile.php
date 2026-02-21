<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'bio',
        'avatar',
        'location',
        'tagline',
        'category',
        'gender',
        'language',
        'is_public',
        'min_rate',
        'featured_until',
    ];

    protected $appends = ['avatar_url', 'is_featured'];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'min_rate' => 'decimal:2',
            'featured_until' => 'datetime',
        ];
    }

    public function getIsFeaturedAttribute(): bool
    {
        return $this->featured_until && $this->featured_until->isFuture();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        // Use relative URL so the image is always requested from the same origin as the page
        // (avoids APP_URL / protocol issues and works behind proxies). Requires public/storage
        // symlink: run `php artisan storage:link` on the server.
        $path = '/storage/' . ltrim($this->avatar, '/');
        $ts = $this->updated_at?->timestamp ?? time();
        return $path . (str_contains($path, '?') ? '&' : '?') . 't=' . $ts;
    }
}
