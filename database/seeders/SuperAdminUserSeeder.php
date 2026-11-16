<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'superadmin@veyraeducate.test'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
            ],
        );

        $this->command?->info('Super admin user: superadmin@veyraeducate.test / password');
    }
}
