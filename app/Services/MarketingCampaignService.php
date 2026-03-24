<?php

namespace App\Services;

use App\Models\MarketingCampaign;
use App\Models\User;
use App\Notifications\MarketingNotification;
use Illuminate\Support\Facades\Log;

class MarketingCampaignService
{
    public function getTargetUsers(MarketingCampaign $campaign)
    {
        $query = User::query();

        switch ($campaign->target_type) {
            case 'individual':
                $query->where('id', $campaign->target_id);
                break;
            case 'role':
                $query->whereHas('roles', function ($q) use ($campaign) {
                    $q->where('roles.id', $campaign->target_id);
                });
                break;
            case 'category':
                $query->whereHas('creatorProfile', function ($q) use ($campaign) {
                    $categoryName = \Illuminate\Support\Facades\DB::table('categories')
                        ->where('id', $campaign->target_id)
                        ->value('name');
                    if ($categoryName) {
                        $q->where('category', $categoryName);
                    }
                });
                break;
            case 'all':
            default:
                break;
        }

        return $query;
    }

    public function dispatchCampaign(MarketingCampaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return;
        }

        $campaign->update(['status' => 'queued']);
        \App\Jobs\SendMarketingCampaignJob::dispatch($campaign);
    }

    public function sendCampaign(MarketingCampaign $campaign)
    {
        $users = $this->getTargetUsers($campaign)->get();
        
        foreach ($users as $user) {
            try {
                $user->notify(new MarketingNotification($campaign));
            } catch (\Exception $e) {
                Log::error("Failed to send marketing notification to user {$user->id}: " . $e->getMessage());
            }
        }

        $campaign->update(['status' => 'completed']);
    }
}
