<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collaboration extends Model
{
    protected $fillable = [
        'brand_id',
        'creator_id',
        'package_id',
        'amount',
        'platform_fee',
        'creator_amount',
        'status',
        'brand_notes',
        'paid_at',
        'rejected_at',
        'coupon_id',
        'revision_count',
        'revision_notes',
        'deliverable_type',
        'deliverable_content',
        'deliverable_preview_path',
        'deliverable_preview_status',
        'delivered_at',
        'completed_at',
        'max_revisions',
        'resolved_refund_amount',
        'resolved_creator_amount',
        'brand_claimed',
        'creator_claimed',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'creator_amount' => 'decimal:2',
            'resolved_refund_amount' => 'decimal:2',
            'resolved_creator_amount' => 'decimal:2',
            'brand_claimed' => 'boolean',
            'creator_claimed' => 'boolean',
            'paid_at' => 'datetime',
            'rejected_at' => 'datetime',
            'delivered_at' => 'datetime',
            'completed_at' => 'datetime',
            'revision_count' => 'integer',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brand_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
