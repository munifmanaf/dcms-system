<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Content Manager',
                'email' => 'content@test.com', 
                'password' => Hash::make('password'),
                'role' => 'content_manager',
            ],
            [
                'name' => 'Technical Reviewer',
                'email' => 'technical@test.com',
                'password' => Hash::make('password'),
                'role' => 'technical_reviewer',
            ],
            [
                'name' => 'Content Reviewer', 
                'email' => 'reviewer@test.com',
                'password' => Hash::make('password'),
                'role' => 'content_reviewer',
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@test.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('Test users created successfully!');
    }
}