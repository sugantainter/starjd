<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackageCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Post',
            'Story',
            'Collaboration Videos',
            'Shoot',
            'Anchoring',
            'Stage Shows',
            'Short Videos',
            'Reels',
            'Unboxing',
            'Review',
            'Other',
        ];

        foreach ($categories as $i => $name) {
            DB::table('package_categories')->insertOrIgnore([
                'name' => $name,
                'slug' => Str::slug($name),
                'sort_order' => $i + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
