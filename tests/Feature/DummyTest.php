<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MarketingCampaign;

class DummyTest extends TestCase
{
    public function test_model_exists()
    {
        $this->assertTrue(class_exists(MarketingCampaign::class));
    }
}
