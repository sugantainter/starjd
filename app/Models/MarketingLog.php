<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingLog extends Model
{
    protected $fillable = [
        'marketing_campaign_id',
        'user_id',
        'type',
        'status',
        'error_message',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
