<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudioImage extends Model
{
    protected $fillable = [
        'studio_id',
        'image',
        'caption',
        'sort_order',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? '/storage/' . ltrim($this->image, '/') : null;
    }
}
