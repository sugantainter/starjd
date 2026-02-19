<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Display the main SPA (Vue) landing page.
     */
    public function index()
    {
        return view('welcome');
    }
}
