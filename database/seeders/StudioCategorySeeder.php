<?php

namespace Database\Seeders;

use App\Models\StudioCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudioCategorySeeder extends Seeder
{
    /**
     * Seed studio categories — scalable beyond "studio":
     * Production, Property Style, Event Spaces, Creative & Personal.
     */
    public function run(): void
    {
        $groups = [
            // Production Studios
            ['Photography Studio', 'Film Studio', 'Podcast Studio', 'Music Recording Studio', 'Rehearsal Studio'],
            // Property Style Spaces
            ['Villa', 'Farmhouse', 'Bungalow', 'Penthouse', 'Warehouse', 'Industrial Loft'],
            // Event Spaces
            ['Banquet Hall', 'Conference Room', 'Workshop Space', 'Rooftop Venue', 'Art Gallery'],
            // Creative & Personal
            ['Dance Studio', 'Yoga Studio', 'Gym Studio', 'Makeup Studio'],
        ];

        $sortOrder = 0;
        foreach ($groups as $names) {
            foreach ($names as $name) {
                StudioCategory::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'is_active' => true, 'sort_order' => $sortOrder++]
                );
            }
        }
    }
}
