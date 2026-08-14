<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@yayasansabah.org',
            'password' => Hash::make('Admin@1234'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Conservation Staff',
            'email' => 'staff@yayasansabah.org',
            'password' => Hash::make('Staff@1234'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);
    }
}
