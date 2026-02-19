<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
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
        ];

        return response()->json($stats);
    }
}
