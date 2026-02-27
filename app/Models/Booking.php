<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAYMENT_PENDING = 'payment_pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    public const CANCELLATION_FLEXIBLE = 'flexible';
    public const CANCELLATION_MODERATE = 'moderate';
    public const CANCELLATION_STRICT = 'strict';

    protected $fillable = [
        'user_id',
        'studio_id',
        'date',
        'start_time',
        'end_time',
        'amount',
        'platform_commission',
        'studio_amount',
        'payment_status',
        'gateway_ref',
        'status',
        'cancellation_policy',
        'customer_notes',
        'confirmed_at',
        'cancelled_at',
        'coupon_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
            'platform_commission' => 'decimal:2',
            'studio_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeNotCancelled($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_REFUNDED]);
    }
}
