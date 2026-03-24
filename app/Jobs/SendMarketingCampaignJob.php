<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MarketingCampaign;
use App\Services\MarketingCampaignService;

class SendMarketingCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct(MarketingCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(MarketingCampaignService $service): void
    {
        if ($this->campaign->status === 'completed') {
            return;
        }

        $this->campaign->update(['status' => 'sending']);

        $service->getTargetUsers($this->campaign)->chunk(100, function ($users) {
            foreach ($users as $user) {
                try {
                    $notification = new \App\Notifications\MarketingNotification($this->campaign);
                    $channels = $notification->via($user);
                    
                    if (empty($channels)) {
                        \App\Models\MarketingLog::create([
                            'marketing_campaign_id' => $this->campaign->id,
                            'user_id' => $user->id,
                            'type' => $this->campaign->type,
                            'status' => 'failed',
                            'error_message' => 'No delivery channels available (missing email or FCM token)',
                        ]);
                        continue;
                    }

                    $user->notify($notification);
                    
                    // Log success
                    \App\Models\MarketingLog::create([
                        'marketing_campaign_id' => $this->campaign->id,
                        'user_id' => $user->id,
                        'type' => $this->campaign->type,
                        'status' => 'sent',
                    ]);
                } catch (\Exception $e) {
                    \App\Models\MarketingLog::create([
                        'marketing_campaign_id' => $this->campaign->id,
                        'user_id' => $user->id,
                        'type' => $this->campaign->type,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }
        });

        $this->campaign->update(['status' => 'completed']);
    }
}
