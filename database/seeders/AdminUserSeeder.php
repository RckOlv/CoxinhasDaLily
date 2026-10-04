<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@lily.com'],
            [
                'name' => 'Lily',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'socio@lily.com'],
            [
                'name' => 'Ricky',
                'password' => Hash::make('socio123'),
                'role' => 'superadmin',
            ]
        );
    }
}