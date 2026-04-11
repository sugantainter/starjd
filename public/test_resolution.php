<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use App\Models\Page;
use App\Models\State;
use App\Models\City;
use App\Http\Controllers\Api\PageController;
use Illuminate\Http\Request;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Request::capture());

$slug = 'influencers';
$stateSlug = 'assam';
$citySlug = 'abhayapuri';

echo "Testing: $slug, State: $stateSlug, City: $citySlug\n";

$state = State::findByUrlSlug($stateSlug);
$city = City::findByUrlSlug($citySlug);

echo "State ID found: " . ($state ? $state->id : 'NOT FOUND') . "\n";
echo "City ID found: " . ($city ? $city->id : 'NOT FOUND') . "\n";

$request = Request::create("/api/pages/$slug", 'GET', [
    'state_slug' => $stateSlug,
    'city_slug' => $citySlug
]);

$controller = new PageController();
$response = $controller->show($request, $slug);

echo "Response Status: " . $response->getStatusCode() . "\n";
$data = $response->original;
if ($data && is_object($data)) {
    echo "Resolved Page ID: " . $data->id . "\n";
    echo "Page Slug: " . $data->slug . "\n";
    echo "City ID: " . ($data->city_id ?? 'NULL') . "\n";
} else {
    echo "No page resolved.\n";
}
