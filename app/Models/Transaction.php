<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'platform_amount',
        'agency_amount',
        'creator_amount',
        'studio_amount',
        'tax',
        'payment_status',
        'gateway',
        'gateway_ref',
        'payable_type',
        'payable_id',
        'user_id',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_amount' => 'decimal:2',
            'agency_amount' => 'decimal:2',
            'creator_amount' => 'decimal:2',
            'studio_amount' => 'decimal:2',
            'tax' => 'decimal:2',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
