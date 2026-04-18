<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@wenza.com'],
            [
                'first_name' => 'Wenza',
                'last_name' => 'Admin',
                'email' => 'admin@wenza.com',
                'password' => Hash::make('WenzaAdmin2026!'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Assign the admin role via Spatie Permission
        $admin->assignRole('admin');
    }
}
