<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@test.com'], // Ensure we don’t duplicate
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password123'), // change if needed
                'role' => 'Admin',
            ]
        );
    }
}
