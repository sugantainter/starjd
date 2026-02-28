<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public const TYPE_ACCESS = 'access';
    public const TYPE_COLLABORATION = 'collaboration';
    public const TYPE_BOOKING = 'booking';

    protected $fillable = [
        'user_id',
        'type',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'status',
        'gateway',
        'txnid',
        'gateway_ref',
        'request_params',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'request_params' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markCompleted(?string $gatewayRef = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'gateway_ref' => $gatewayRef ?? $this->gateway_ref,
            'completed_at' => now(),
        ]);
    }

    public function markFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'completed_at' => now(),
        ]);
    }
}
