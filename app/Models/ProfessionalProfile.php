<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalProfile extends Model
{
    protected $fillable = [
        'user_id',
        'tagline',
        'bio',
        'languages',
        'skills',
        'education',
        'certifications',
        'response_time',
        'avg_rating',
        'total_reviews',
    ];

    protected $casts = [
        'languages' => 'array',
        'skills' => 'array',
        'education' => 'array',
        'certifications' => 'array',
        'avg_rating' => 'float',
        'total_reviews' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
