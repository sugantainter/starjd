<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatorProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array (public list item).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource->user;
        $totalFollowers = $user && $user->relationLoaded('socialAccounts')
            ? (int) $user->socialAccounts->where('is_connected', true)->sum('followers_count')
            : ($user ? (int) $user->socialAccounts()->where('is_connected', true)->sum('followers_count') : 0);
        $avgRating = $this->resource->average_rating ?? $this->resource->reviews()->approved()->avg('rating');
        $minPrice = $this->resource->min_rate ?? $this->resource->user?->packages()->where('is_active', true)->min('price');

        return [
            'id' => $this->resource->id,
            'slug' => $this->resource->slug,
            'bio' => $this->resource->bio,
            'avatar' => $this->resource->avatar,
            'avatar_url' => $this->resource->avatar_url,
            'location' => $this->resource->location,
            'tagline' => $this->resource->tagline,
            'category' => $this->resource->category,
            'gender' => $this->resource->gender,
            'language' => $this->resource->language,
            'min_rate' => $this->resource->min_rate ? (float) $this->resource->min_rate : null,
            'engagement_rate' => $this->resource->engagement_rate ? (float) $this->resource->engagement_rate : null,
            'verification_status' => $this->resource->verification_status,
            'is_featured' => $this->resource->is_featured,
            'featured_until' => $this->resource->featured_until?->toIso8601String(),
            'total_followers' => $totalFollowers,
            'average_rating' => $avgRating !== null && $avgRating > 0 ? round((float) $avgRating, 2) : null,
            'min_price' => $minPrice !== null ? (float) $minPrice : null,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
            ] : null,
            'packages_count' => $user ? $user->packages()->where('is_active', true)->count() : 0,
        ];
    }
}
