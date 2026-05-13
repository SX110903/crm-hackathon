<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@hackathon.com';

        User::updateOrCreate(
            ['email_hash' => hash('sha256', strtolower(trim($email)))],
            [
                'name'      => 'Administrator',
                'email'     => $email,
                'password'  => 'Admin1234!',
                'role'      => 'admin',
                'is_active' => true,
            ]
        );
    }
}
