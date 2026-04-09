<?php

namespace App\Models;

use App\Support\StoragePublicUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'slug',
        'logo',
        'website',
        'bio',
        'industry',
        'hq_location',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected $appends = ['logo_url'];

    protected static function booted(): void
    {
        static::creating(function (BrandProfile $profile) {
            if (blank($profile->slug) && $profile->user_id) {
                $user = User::query()->find($profile->user_id);
                if ($user) {
                    $profile->slug = static::generateUniqueSlugForUser($user);
                }
            }
        });
    }

    public static function generateUniqueSlugForUser(User $user, ?int $exceptProfileId = null): string
    {
        $base = \Illuminate\Support\Str::slug($user->name ?: 'brand');
        $base = rtrim(substr($base, 0, 180), '-');
        if ($base === '') {
            $base = 'brand';
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

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        $url = StoragePublicUrl::resolve($this->logo);
        if ($url === null) {
            return null;
        }
        $ts = $this->updated_at?->timestamp;

        return $ts ? $url.(str_contains($url, '?') ? '&' : '?').'t='.$ts : $url;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
