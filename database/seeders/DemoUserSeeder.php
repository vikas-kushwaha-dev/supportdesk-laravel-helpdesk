<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'agent@example.com'],
            [
                'name' => 'Support Agent',
                'password' => Hash::make('password'),
                'role' => User::ROLE_AGENT,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'email_verified_at' => now(),
            ]
        );
    }
}
