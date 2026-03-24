<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MarketingCampaign;
use App\Models\User;
use App\Services\MarketingCampaignService;

try {
    echo "Starting Diagnostic...\n";
    $service = new MarketingCampaignService();
    
    // Create a dummy campaign
    $campaign = new MarketingCampaign([
        'title' => 'Diagnostic',
        'content' => 'Test',
        'type' => 'email',
        'target_type' => 'all'
    ]);
    
    echo "Getting Target Users...\n";
    $users = $service->getTargetUsers($campaign);
    echo "Count: " . $users->count() . "\n";
    
    echo "Diagnostic Complete.\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " LINE: " . $e->getLine() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
