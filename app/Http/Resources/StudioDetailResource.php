<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudioDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array (public detail).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mainImage = $this->resource->images()->where('is_primary', true)->first()
            ?? $this->resource->images()->orderBy('sort_order')->first();

        $data = [
            'id' => $this->resource->id,
            'slug' => $this->resource->slug,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'main_image' => $mainImage ? '/storage/' . ltrim($mainImage->image, '/') : null,
            'price_per_hour' => $this->resource->price_per_hour ? (float) $this->resource->price_per_hour : null,
            'price_per_day' => $this->resource->price_per_day ? (float) $this->resource->price_per_day : null,
            'cancellation_policy' => $this->resource->cancellation_policy ?? 'moderate',
            'rating_avg' => $this->resource->rating_avg ? (float) $this->resource->rating_avg : null,
            'reviews_count' => (int) ($this->resource->reviews_count ?? 0),
            'category' => $this->categoryModel() ? [
                'id' => $this->categoryModel()->id,
                'name' => $this->categoryModel()->name,
                'slug' => $this->categoryModel()->slug,
            ] : null,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'address' => $this->resource->address,
            'pincode' => $this->resource->pincode,
            'latitude' => $this->resource->latitude ? (float) $this->resource->latitude : null,
            'longitude' => $this->resource->longitude ? (float) $this->resource->longitude : null,
            'featured' => (bool) $this->resource->is_featured,
            'gallery' => $this->resource->images->map(fn ($img) => [
                'id' => $img->id,
                'image' => '/storage/' . ltrim($img->image, '/'),
                'caption' => $img->caption,
                'is_primary' => $img->is_primary,
                'sort_order' => $img->sort_order,
            ]),
            'amenities' => $this->resource->amenities->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'slug' => $a->slug,
                'icon' => $a->icon,
            ]),
            'owner' => $this->resource->user ? [
                'id' => $this->resource->user->id,
                'name' => $this->resource->user->name,
            ] : null,
        ];

        if ($this->resource->relationLoaded('reviews')) {
            $data['reviews'] = $this->resource->reviews->map(fn ($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'user_name' => $r->user?->name,
                'created_at' => $r->created_at->toIso8601String(),
            ]);
        }

        if ($this->resource->relationLoaded('availabilitySlots')) {
            $data['availability_slots'] = $this->resource->availabilitySlots
                ->where('is_available', true)
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'date' => $s->date->format('Y-m-d'),
                    'start_time' => $s->start_time,
                    'end_time' => $s->end_time,
                ])
                ->values();
        }

        return $data;
    }

    /** Get the category relation (Studio has a string column "category" that shadows the relationship). */
    private function categoryModel(): ?object
    {
        $studio = $this->resource;
        if ($studio->relationLoaded('category')) {
            return $studio->getRelation('category');
        }

        return $studio->category()->first();
    }
}
