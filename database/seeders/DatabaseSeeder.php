<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@aduutech.com'],
            [
                'name' => 'Aduu Tech Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@2025!'),
            ]
        );

        $this->call(RoleSeeder::class);
    }
}
