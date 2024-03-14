<?php

namespace Database\Seeders\Core;

use App\Models\Client\Enums\AccessRole;
use App\Models\Core\Field;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);

        $superAdmin = Role::query()->where('name', AccessRole::SuperAdmin)->first();
        $manager = Role::query()->where('name', AccessRole::Manager)->first();

        Field::query()->get()->each(function (Field $field) use ($manager, $superAdmin) {
            $fullName = "$field->entity_id.$field->identifier";

            $superAdmin->givePermissionTo("view entity-field $fullName");
            $superAdmin->givePermissionTo("edit entity-field $fullName");

            if ($field->entity_id !== 'equity' && $fullName !== 'employee.manager' && $fullName !== 'employee.equity') {
                $manager->givePermissionTo("view entity-field $fullName");
                $manager->givePermissionTo("edit entity-field $fullName");
            }
        });
    }
}
