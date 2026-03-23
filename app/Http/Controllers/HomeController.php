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
        $pages = \App\Models\Page::published()->inRandomOrder()->limit(18)->get(['id', 'title', 'slug']);
        return view('welcome', compact('pages'));
    }
}
