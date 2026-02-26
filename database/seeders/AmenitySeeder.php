<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    /**
     * Seed amenities: Basic, Equipment, Add-ons (admin can add/remove later).
     */
    public function run(): void
    {
        $groups = [
            // Basic Amenities
            ['Air Conditioning', 'WiFi', 'Parking', 'Power Backup', 'Washroom', 'Changing Room'],
            // Equipment
            ['Softbox Lights', 'Green Screen', 'DSLR Available', 'Microphones', 'Soundproofing', 'Props'],
            // Add-ons (paid extras; admin can mark as paid later)
            ['Photographer', 'Videographer', 'Editor', 'Makeup Artist', 'Catering'],
        ];

        $sortOrder = 0;
        foreach ($groups as $names) {
            foreach ($names as $name) {
                Amenity::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'is_active' => true, 'sort_order' => $sortOrder++]
                );
            }
        }
    }
}
