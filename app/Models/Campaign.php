<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'brand_id',
        'title',
        'slug',
        'description',
        'deliverables',
        'budget',
        'status',
        'starts_at',
        'ends_at',
        'max_applications',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brand_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CampaignApplication::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
