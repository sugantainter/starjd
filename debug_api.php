<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\MarketingController;
use App\Services\MarketingCampaignService;

try {
    $controller = new MarketingController(new MarketingCampaignService());
    $response = $controller->getFilters();
    echo "Filters Response:\n";
    print_r($response->getData());
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
