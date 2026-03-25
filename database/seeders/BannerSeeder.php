<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Banner::create([
            'title' => 'Top Creators of the Month',
            'image' => 'https://images.unsplash.com/photo-1542744094-24638eff58bb?w=1000&q=80',
            'link' => '/explore',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        
        \App\Models\Banner::create([
            'title' => 'Book Your Dream Studio',
            'image' => 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=1000&q=80',
            'link' => '/studios',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        \App\Models\Banner::create([
            'title' => 'Exclusive Campaign for Brands',
            'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1000&q=80',
            'link' => '/campaigns',
            'sort_order' => 3,
            'is_active' => true,
        ]);
    }
}
