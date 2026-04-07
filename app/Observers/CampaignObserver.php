<?php

namespace App\Observers;

use App\Models\Campaign;
use App\Models\User;
use App\Mail\CampaignMatchMail;
use Illuminate\Support\Facades\Mail;

class CampaignObserver
{
    /**
     * Handle the Campaign "updated" event.
     */
    public function updated(Campaign $campaign)
    {
        // Trigger only when status changes to "open"
        if ($campaign->isDirty('status') && $campaign->status === 'open') {
            // Find creators whose profile category or sub_category matches any of the campaign niches
            $niches = $campaign->targeting['niches'] ?? [];
            if (empty($niches)) {
                return;
            }

            $creators = User::whereHas('creatorProfile', function ($q) use ($niches) {
                $q->whereIn('category', $niches)
                  ->orWhereIn('sub_category', $niches);
            })->get();

            foreach ($creators as $creator) {
                Mail::to($creator)->queue(new CampaignMatchMail($creator, $campaign));
            }
        }
    }
}
?>
