<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudioResource extends JsonResource
{
    /**
     * Transform the resource into an array (public list item).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mainImage = $this->resource->images()->where('is_primary', true)->first()
            ?? $this->resource->images()->orderBy('sort_order')->first();

        return [
            'id' => $this->resource->id,
            'slug' => $this->resource->slug,
            'name' => $this->resource->name,
            'description' => $this->resource->description ? \Illuminate\Support\Str::limit($this->resource->description, 160) : null,
            'main_image' => $mainImage ? '/storage/' . ltrim($mainImage->image, '/') : null,
            'price_per_hour' => $this->resource->price_per_hour ? (float) $this->resource->price_per_hour : null,
            'price_per_day' => $this->resource->price_per_day ? (float) $this->resource->price_per_day : null,
            'rating_avg' => $this->resource->rating_avg ? (float) $this->resource->rating_avg : null,
            'reviews_count' => (int) ($this->resource->reviews_count ?? 0),
            'category' => $this->categoryArray(),
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'featured' => (bool) $this->resource->is_featured,
            'latitude' => $this->resource->latitude ? (float) $this->resource->latitude : null,
            'longitude' => $this->resource->longitude ? (float) $this->resource->longitude : null,
        ];
    }

    /**
     * Category from relationship (StudioCategory), not the legacy string attribute.
     *
     * @return array{id: int, name: string, slug: string}|null
     */
    private function categoryArray(): ?array
    {
        $category = $this->resource->relationLoaded('category')
            ? $this->resource->getRelation('category')
            : $this->resource->category()->first();

        if (! $category || ! is_object($category)) {
            return null;
        }

        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
        ];
    }
}
