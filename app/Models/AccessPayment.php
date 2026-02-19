<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessPayment extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'status',
        'paid_at',
        'gateway_ref',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function hasPaidAccess(User $user): bool
    {
        return static::where('user_id', $user->id)
            ->where('type', $user->role)
            ->where('status', 'paid')
            ->exists();
    }
}
