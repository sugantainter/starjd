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
        'followers_count',
        'is_connected',
    ];

    protected $hidden = ['access_token'];

    protected function casts(): array
    {
        return [
            'is_connected' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
