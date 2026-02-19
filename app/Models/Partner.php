<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['name', 'logo', 'link', 'sort_order'];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }
        if (is_string($this->logo) && (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://'))) {
            return $this->logo;
        }
        return asset('storage/' . $this->logo);
    }
}
