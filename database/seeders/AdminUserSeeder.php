<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@heribrennholzgmbh.com');
        $password = (string) env('ADMIN_PASSWORD', 'HbBank!ChangeMe2026');
        $name = (string) env('ADMIN_NAME', 'Administrator');

        // Passwort wird durch das Model-Cast "hashed" gehasht – nicht vorab hashen.
        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
            ]
        );
    }
}
