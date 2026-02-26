<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'gst_number',
        'address',
        'city',
        'state',
        'pincode',
        'website',
        'kyc_documents',
        'approval_status',
        'commission_percent',
    ];

    protected function casts(): array
    {
        return [
            'commission_percent' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creatorProfiles(): HasMany
    {
        return $this->hasMany(CreatorProfile::class, 'agency_id');
    }
}
