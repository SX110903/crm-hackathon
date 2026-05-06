<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@hackathon.com'],
            [
                'name'      => 'Administrator',
                'email'     => 'admin@hackathon.com',
                'password'  => Hash::make('Admin1234!'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );
    }
}
