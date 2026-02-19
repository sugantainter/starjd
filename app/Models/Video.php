<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'video_id',
        'title',
        'desc',
        'embed_url',
        'watch_url',
        'sort_order',
    ];
}
