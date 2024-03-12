<?php

namespace Database\Seeders\Core;

use App\Models\Client\Enums\AccessRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AccessRole::cases() as $case) {
            Role::create(['name' => $case->value]);
        }
    }
}
