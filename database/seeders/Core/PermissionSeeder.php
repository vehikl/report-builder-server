<?php

namespace Database\Seeders\Core;

use App\Models\Core\Field;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Field::query()->get()->each(function (Field $field) {
            Permission::create(['name' => "view entity-field $field->full_name"]);
            Permission::create(['name' => "edit entity-field $field->full_name"]);
        });
    }
}
