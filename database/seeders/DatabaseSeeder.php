<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Existing test user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'role' => 'Member',
            ]
        );

        // Additional members
        User::updateOrCreate(
            ['email' => 'member1@test.com'],
            [
                'name' => 'Member One',
                'password' => Hash::make('password123'),
                'role' => 'Member',
            ]
        );

        User::updateOrCreate(
            ['email' => 'member2@test.com'],
            [
                'name' => 'Member Two',
                'password' => Hash::make('password123'),
                'role' => 'Member',
            ]
        );

        // Admin user
        $this->call(AdminUserSeeder::class);
    }
}
