<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    // Allow mass assignment from BannerController.
    // (Controller uses $banner->update($validated) / Banner::create($validated))
    protected $fillable = [
        'title',
        'link',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
