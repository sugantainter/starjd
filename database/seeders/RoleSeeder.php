<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed RBAC roles. Safe to run multiple times (insertOrIgnore).
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Platform administrator'],
            ['name' => 'Brand', 'slug' => 'brand', 'description' => 'Brand / advertiser'],
            ['name' => 'Creator', 'slug' => 'creator', 'description' => 'Content creator'],
            ['name' => 'Agency', 'slug' => 'agency', 'description' => 'Agency managing creators'],
            ['name' => 'Studio Owner', 'slug' => 'studio_owner', 'description' => 'Studio listing owner'],
            ['name' => 'Customer', 'slug' => 'customer', 'description' => 'End customer / booker'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
