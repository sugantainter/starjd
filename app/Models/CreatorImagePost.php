<?php

namespace App\Models;

use App\Support\StoragePublicUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorImagePost extends Model
{
    protected $fillable = [
        'creator_id',
        'image',
        'caption',
        'sort_order',
    ];

    protected $appends = ['image_url'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getImageUrlAttribute(): string
    {
        return StoragePublicUrl::resolve($this->image) ?? '';
    }
}
