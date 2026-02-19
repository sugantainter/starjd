<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedPayment extends Model
{
    protected $table = 'featured_payments';

    protected $fillable = [
        'creator_id',
        'plan_id',
        'amount',
        'duration_days',
        'featured_until',
        'status',
        'paid_at',
        'gateway_ref',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'featured_until' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
