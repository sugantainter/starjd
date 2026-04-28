<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Hospital extends Model
{
    protected $table = 'hospitals';
    protected $fillable = ['city', 'area', 'state', 'name', 'latitude', 'longitude', 'rating', 'vicinity'];

    public function seoPage(): MorphOne
    {
        return $this->morphOne(SeoPage::class, 'entity');
    }
}
