<?php

namespace App\Models;

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

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->logo);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
