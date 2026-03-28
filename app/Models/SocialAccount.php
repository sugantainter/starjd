<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'username',
        'profile_url',
        'access_token',
        'refresh_token',
        'expires_at',
        'token_secret',
        'followers_count',
        'is_connected',
        'analytics_data',
    ];

    protected $hidden = ['access_token', 'refresh_token', 'token_secret'];

    protected function casts(): array
    {
        return [
            'is_connected' => 'boolean',
            'expires_at' => 'datetime',
            'analytics_data' => 'array',
            'followers_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
