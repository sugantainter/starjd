<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REVISION_REQUESTED = 'revision_requested';

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'service_listing_id',
        'package_type',
        'amount',
        'status',
        'requirements',
        'delivery_files',
        'payment_status',
        'due_at',
        'revisions_remaining',
    ];

    protected $casts = [
        'delivery_files' => 'array',
        'due_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(ServiceListing::class, 'service_listing_id');
    }
}
