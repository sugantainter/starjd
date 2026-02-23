<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = ['name', 'slug', 'code', 'sort_order'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
