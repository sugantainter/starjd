<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'shine@starjd.com')->exists()) {
            return;
        }

        $user = User::create([
            'name' => 'Admin',
            'email' => 'shine@starjd.com',
            'password' => Hash::make('starjdarsv12488@#546!!'),
        ]);
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $user->roles()->attach($adminRole->id, ['is_primary' => true]);
        }
    }
}
