<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(SectionsSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(PlatformSettingsSeeder::class);
        $this->call(PackageCategorySeeder::class);
        $this->call(StudioCategorySeeder::class);
        $this->call(AmenitySeeder::class);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $creatorRole = Role::where('slug', 'creator')->first();
        if ($creatorRole) {
            $user->roles()->attach($creatorRole->id, ['is_primary' => true]);
        }
    }
}
