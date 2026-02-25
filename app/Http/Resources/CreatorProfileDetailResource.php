<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatorProfileDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array (public profile detail).
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
        $reviewsCount = $this->resource->reviews_count ?? $this->resource->reviews()->approved()->count();

        $data = [
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
            'total_followers' => $totalFollowers,
            'average_rating' => $avgRating !== null && $avgRating > 0 ? round((float) $avgRating, 2) : null,
            'reviews_count' => (int) $reviewsCount,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'social_accounts' => $user->socialAccounts->map(fn ($a) => [
                    'platform' => $a->platform,
                    'username' => $a->username,
                    'profile_url' => $a->profile_url,
                    'followers_count' => $a->followers_count,
                    'is_connected' => $a->is_connected,
                ]),
            ] : null,
        ];

        if ($this->resource->relationLoaded('user')) {
            $user = $this->resource->user;
            if ($user && $user->relationLoaded('packages')) {
                $data['packages'] = $user->packages->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (float) $p->price,
                    'description' => $p->description,
                    'deliverables' => $p->deliverables,
                    'category' => $p->packageCategory?->name,
                    'items' => $p->items->map(fn ($i) => [
                        'name' => $i->name,
                        'quantity' => $i->quantity,
                        'unit_price' => (float) $i->unit_price,
                    ]),
                ]);
            }
            if ($user && $user->relationLoaded('creatorImagePosts')) {
                $data['portfolio'] = $user->creatorImagePosts->map(fn ($p) => [
                    'id' => $p->id,
                    'image' => $p->image ? '/storage/' . ltrim($p->image, '/') : null,
                    'caption' => $p->caption,
                    'sort_order' => $p->sort_order,
                ]);
            }
        }

        return $data;
    }
}
