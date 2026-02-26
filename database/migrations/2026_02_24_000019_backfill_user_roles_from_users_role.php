<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Backfill user_roles from existing users.role; seed default roles if missing.
     */
    public function up(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Platform administrator'],
            ['name' => 'Brand', 'slug' => 'brand', 'description' => 'Brand / advertiser'],
            ['name' => 'Creator', 'slug' => 'creator', 'description' => 'Content creator'],
            ['name' => 'Agency', 'slug' => 'agency', 'description' => 'Agency managing creators'],
            ['name' => 'Studio Owner', 'slug' => 'studio_owner', 'description' => 'Studio listing owner'],
            ['name' => 'Customer', 'slug' => 'customer', 'description' => 'End customer / booker'],
        ];

        $now = now();
        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore([
                'name' => $role['name'],
                'slug' => $role['slug'],
                'description' => $role['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        $roleIds = DB::table('roles')->pluck('id', 'slug');
        $map = [
            'admin' => 'admin',
            'creator' => 'creator',
            'brand' => 'brand',
            'agency' => 'agency',
            'studio_owner' => 'studio_owner',
            'studioowner' => 'studio_owner',
            'customer' => 'customer',
        ];

        foreach (DB::table('users')->get() as $user) {
            $slug = $map[strtolower($user->role ?? '')] ?? 'customer';
            $roleId = $roleIds[$slug] ?? $roleIds['customer'];
            $exists = DB::table('user_roles')
                ->where('user_id', $user->id)
                ->where('role_id', $roleId)
                ->exists();
            if (! $exists) {
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'is_primary' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('user_roles')->truncate();
        DB::table('roles')->truncate();
    }
};
