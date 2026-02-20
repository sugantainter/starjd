<?php

namespace Database\Seeders;

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

        User::create([
            'name' => 'Admin',
            'email' => 'shine@starjd.com',
            'password' => Hash::make('starjdarsv12488@#546!!'),
            'role' => 'admin',
        ]);
    }
}
