<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Metro extends Model
{
    protected $table = 'metros';
    protected $fillable = ['city', 'area', 'state', 'name', 'latitude', 'longitude', 'rating', 'vicinity'];

    public function seoPage(): MorphOne
    {
        return $this->morphOne(SeoPage::class, 'entity');
    }
}
