<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@starjd.test')->exists()) {
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@starjd.test',
            'password' => Hash::make('starjdarsv12488@#546!!'),
            'role' => 'admin',
        ]);
    }
}
