<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignApplication extends Model
{
    protected $fillable = [
        'campaign_id',
        'creator_id',
        'cover_message',
        'quoted_amount',
        'status',
        'brand_notes',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'quoted_amount' => 'decimal:2',
            'responded_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
