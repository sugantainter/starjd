<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIUsage extends Model
{
    protected $table = 'ai_usages';

    protected $fillable = [
        'user_id',
        'provider',
        'model',
        'type',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
