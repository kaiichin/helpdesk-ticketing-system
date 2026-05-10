<?php

namespace Database\Seeders;

use App\Models\Category;
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
        foreach (['Hardware', 'Software', 'Network', 'Account', 'Other'] as $categoryName) {
            Category::firstOrCreate([
                'name' => $categoryName,
            ]);
        }

        User::updateOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee User',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        User::updateOrCreate(
            ['email' => 'support@example.com'],
            [
                'name' => 'IT Support',
                'password' => Hash::make('password'),
                'role' => 'it_support',
            ]
        );
    }
}
