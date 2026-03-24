<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'target_type',
        'target_id',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(MarketingLog::class);
    }
}
