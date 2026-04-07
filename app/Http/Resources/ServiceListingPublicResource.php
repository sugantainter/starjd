<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Support\StoragePublicUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceListingPublicResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $listing = $this->resource;

        return [
            'id' => $listing->id,
            'user_id' => $listing->user_id,
            'service_id' => $listing->service_id,
            'title' => $listing->title,
            'slug' => $listing->slug,
            'description' => $listing->description
                ? StoragePublicUrl::rewriteStorageUrlsInHtml($listing->description)
                : $listing->description,
            'pricing_tiers' => self::rewritePricingTiersHtml($listing->pricing_tiers),
            'gallery' => $listing->resolvedGalleryUrls(),
            'faqs' => $listing->faqs,
            'metadata' => $listing->metadata,
            'tags' => $listing->tags,
            'is_active' => $listing->is_active,
            'created_at' => $listing->created_at,
            'updated_at' => $listing->updated_at,
            'user' => $this->whenLoaded('user', fn () => self::serializePublicUser($listing->user)),
            'service_category' => $this->whenLoaded('serviceCategory', fn () => $listing->serviceCategory?->toArray()),
            'category' => $this->whenLoaded('serviceCategory', fn () => $listing->serviceCategory?->only(['id', 'name', 'slug'])),
        ];
    }

    /**
     * @param  mixed  $tiers
     * @return mixed
     */
    /**
     * Public user payload for gig pages (rich text in nested profile).
     */
    private static function serializePublicUser(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        $data = $user->toArray();

        unset($data['email'], $data['email_verified_at'], $data['fcm_token']);

        $profile = $data['professional_profile'] ?? null;
        if (is_array($profile)
            && ! empty($profile['bio'])
            && is_string($profile['bio'])) {
            $data['professional_profile']['bio'] = StoragePublicUrl::rewriteStorageUrlsInHtml($profile['bio']);
        }

        return $data;
    }

    private static function rewritePricingTiersHtml(mixed $tiers): mixed
    {
        if (! is_array($tiers)) {
            return $tiers;
        }

        return array_map(static function ($tier) {
            if (! is_array($tier)) {
                return $tier;
            }
            if (! empty($tier['description']) && is_string($tier['description'])) {
                $tier['description'] = StoragePublicUrl::rewriteStorageUrlsInHtml($tier['description']);
            }

            return $tier;
        }, $tiers);
    }
}
