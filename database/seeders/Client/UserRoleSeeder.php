<?php

namespace Database\Seeders\Client;

use App\Models\Client\Enums\AccessRole;
use App\Models\Client\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        /** @var User $user */
        $user = User::query()->create([
            'name' => 'super',
            'email' => 'super@user',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole(AccessRole::SuperAdmin, AccessRole::Manager);
    }
}
