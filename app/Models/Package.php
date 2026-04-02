<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'creator_id',
        'package_category_id',
        'name',
        'price',
        'description',
        'deliverables',
        'is_active',
        'sort_order',
        'is_negotiable',
        'revisions',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_negotiable' => 'boolean',
            'revisions' => 'integer',
        ];
    }

    protected $appends = ['items_total'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function packageCategory(): BelongsTo
    {
        return $this->belongsTo(PackageCategory::class, 'package_category_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackageItem::class)->orderBy('sort_order');
    }

    public function collaborations(): HasMany
    {
        return $this->hasMany(Collaboration::class);
    }

    /** Sum of all item line totals (quantity × unit_price). */
    public function getItemsTotalAttribute(): float
    {
        return (float) $this->items->sum(fn (PackageItem $item) => $item->quantity * $item->unit_price);
    }
}
