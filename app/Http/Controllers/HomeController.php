<?php

namespace App\Http\Controllers;

use App\Models\Page;

class HomeController extends Controller
{
    /**
     * Display the main SPA (Vue) landing page.
     */
    public function index()
    {
        $pages = Page::published()
            ->with(['state:id,slug', 'city:id,slug'])
            ->inRandomOrder()
            ->limit(18)
            ->get(['id', 'title', 'slug', 'state_id', 'city_id']);
        return view('welcome', compact('pages'));
    }
}
