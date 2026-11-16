<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@veyraeducate.test'],
            [
                'name' => 'Admin Veyra',
                'password' => 'password',
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
            ],
        );

        $this->command?->info('Admin user: admin@veyraeducate.test / password');
    }
}
