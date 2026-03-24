<?php

namespace Tests\Feature;

use App\Models\MarketingCampaign;
use App\Models\Role;
use App\Models\User;
use App\Models\CreatorProfile;
use App\Services\MarketingCampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MarketingNotification;

class MarketingCampaignTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MarketingCampaignService();
    }

    public function test_it_filters_users_by_role()
    {
        $role = Role::create(['name' => 'Creator', 'slug' => 'creator']);
        $user1 = User::factory()->create();
        $user1->roles()->attach($role);
        
        $user2 = User::factory()->create(); // No role

        $campaign = MarketingCampaign::create([
            'title' => 'Role Test',
            'content' => 'Test',
            'type' => 'email',
            'target_type' => 'role',
            'target_id' => $role->id,
        ]);

        $users = $this->service->getTargetUsers($campaign)->get();

        $this->assertCount(1, $users);
        $this->assertEquals($user1->id, $users->first()->id);
    }

    public function test_it_filters_all_users()
    {
        User::factory()->count(3)->create();

        $campaign = MarketingCampaign::create([
            'title' => 'All Test',
            'content' => 'Test',
            'type' => 'email',
            'target_type' => 'all',
        ]);

        $users = $this->service->getTargetUsers($campaign)->get();

        $this->assertCount(3, $users);
    }

    public function test_it_dispatches_notifications()
    {
        Notification::fake();

        $user = User::factory()->create();
        $campaign = MarketingCampaign::create([
            'title' => 'Notify Test',
            'content' => 'Test Message',
            'type' => 'email',
            'target_type' => 'all',
        ]);

        $this->service->sendCampaign($campaign);

        Notification::assertSentTo(
            [$user], MarketingNotification::class
        );
        
        $this->assertEquals('completed', $campaign->fresh()->status);
    }

    public function test_admin_can_create_campaign()
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $admin->roles()->attach($role);

        $response = $this->actingAs($admin)->postJson('/api/admin/marketing', [
            'title' => 'API Test',
            'content' => 'API Content',
            'type' => 'push',
            'target_type' => 'all',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('marketing_campaigns', ['title' => 'API Test']);
    }
}
