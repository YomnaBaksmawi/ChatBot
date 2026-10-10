<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (empty($email) || empty($password)) {
            $this->command?->warn('AdminSeeder skipped: set ADMIN_EMAIL and ADMIN_PASSWORD in the environment before seeding an administrator account.');

            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('ADMIN_EMAIL must be a valid email address.');
        }

        if (strlen($password) < 12) {
            throw new \InvalidArgumentException('ADMIN_PASSWORD must be at least 12 characters long.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'password' => Hash::make($password),
                'role' => env('ADMIN_ROLE', 'ADMIN'),
            ]
        );
    }
}