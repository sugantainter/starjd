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
        'logo',
        'website',
        'bio',
    ];

    protected $appends = ['logo_url'];

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
