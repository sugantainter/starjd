<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\Studio;

class ReviewObserver
{
    public function saved(Review $review): void
    {
        $this->refreshReviewableCache($review);
    }

    public function deleted(Review $review): void
    {
        $this->refreshReviewableCache($review);
    }

    private function refreshReviewableCache(Review $review): void
    {
        if ($review->reviewable_type !== Studio::class) {
            return;
        }

        $studio = Studio::find($review->reviewable_id);
        if (! $studio) {
            return;
        }

        $avg = $studio->reviews()->approved()->avg('rating');
        $count = $studio->reviews()->approved()->count();

        $studio->update([
            'rating_avg' => $avg ? round((float) $avg, 2) : null,
            'reviews_count' => $count,
        ]);
    }
}
