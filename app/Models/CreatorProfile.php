<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class CreatorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'agency_id',
        'slug',
        'bio',
        'avatar',
        'location',
        'tagline',
        'category',
        'sub_category',
        'gender',
        'language',
        'is_public',
        'min_rate',
        'engagement_rate',
        'verification_status',
        'featured_until',
    ];

    protected $appends = ['avatar_url', 'is_featured'];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'min_rate' => 'decimal:2',
            'engagement_rate' => 'decimal:2',
            'featured_until' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CreatorProfile $profile) {
            if (blank($profile->slug) && $profile->user_id) {
                $user = User::query()->find($profile->user_id);
                if ($user) {
                    $profile->slug = static::generateUniqueSlugForUser($user);
                }
            }
        });
    }

    /**
     * Build a URL-safe slug from the user's name + id, ensuring uniqueness across creator_profiles.
     */
    public static function generateUniqueSlugForUser(User $user, ?int $exceptProfileId = null): string
    {
        $base = Str::slug($user->name);
        if ($base === '') {
            $base = 'creator';
        }
        $base = rtrim(substr($base, 0, 180), '-');
        if ($base === '') {
            $base = 'creator';
        }

        $suffix = '-' . $user->id;
        $candidate = $base . $suffix;

        $slugTaken = static function (string $slug) use ($exceptProfileId): bool {
            $q = static::query()->where('slug', $slug);
            if ($exceptProfileId !== null) {
                $q->where('id', '!=', $exceptProfileId);
            }

            return $q->exists();
        };

        if (! $slugTaken($candidate)) {
            return $candidate;
        }

        $n = 2;
        do {
            $candidate = $base . $suffix . '-' . $n;
            $n++;
        } while ($slugTaken($candidate));

        return $candidate;
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Total followers from connected social accounts (for display/sort).
     */
    public function getTotalFollowersAttribute(): int
    {
        return (int) $this->user?->socialAccounts()
            ->where('is_connected', true)
            ->sum('followers_count');
    }

    /**
     * Average rating from approved reviews (for display/sort).
     */
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->approved()->avg('rating');
        return $avg !== null ? round((float) $avg, 2) : null;
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

        $url = \Illuminate\Support\Facades\Storage::url($this->avatar);
        $ts = $this->updated_at?->timestamp ?? time();
        return $url . (str_contains($url, '?') ? '&' : '?') . 't=' . $ts;
    }
}
