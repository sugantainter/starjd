<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'categories' => DB::table('categories')->count(),
            'testimonials' => DB::table('testimonials')->count(),
            'faqs' => DB::table('faqs')->count(),
            'steps' => DB::table('steps')->count(),
            'contacts' => DB::table('contact_messages')->count(),
            'posts' => Post::count(),
            'posts_published' => Post::published()->count(),
            'pages' => Page::count(),
            'pages_published' => Page::published()->count(),
            'states' => State::count(),
            'cities' => City::count(),
            'videos' => DB::table('videos')->count(),
            'partners' => DB::table('partners')->count(),
            'services' => DB::table('services')->count(),
        ];

        return response()->json($stats);
    }
}
