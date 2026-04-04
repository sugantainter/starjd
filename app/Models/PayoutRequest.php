<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PayoutRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'collaboration_id',
        'bank_account_id',
        'amount',
        'type',
        'status',
        'admin_notes',
        'receipt_url',
        'processed_at'
    ];
    
    protected $appends = ['receipt_full_url'];

    public function getReceiptFullUrlAttribute(): ?string
    {
        return $this->receipt_url ? \Illuminate\Support\Facades\Storage::url($this->receipt_url) : null;
    }

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collaboration()
    {
        return $this->belongsTo(Collaboration::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
