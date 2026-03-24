<?php

namespace Tests\Feature;

use App\Models\MarketingCampaign;
use App\Models\User;
use App\Services\MarketingCampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinimizedMarketingTest extends TestCase
{
    // use RefreshDatabase;

    public function test_it_filters_all_users()
    {
        User::factory()->count(2)->create();

        $campaign = MarketingCampaign::create([
            'title' => 'All Test',
            'content' => 'Test',
            'type' => 'email',
            'target_type' => 'all',
        ]);

        $service = new MarketingCampaignService();
        $users = $service->getTargetUsers($campaign)->get();

        $this->assertCount(2, $users);
    }
}
