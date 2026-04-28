<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Area extends Model
{
    protected $table = 'areas';
    protected $fillable = ['place_id', 'name', 'address', 'city_id', 'city', 'state_id', 'state', 'types', 'latitude', 'longitude', 'status'];

    public function seoPage(): MorphOne
    {
        return $this->morphOne(SeoPage::class, 'entity');
    }
}
