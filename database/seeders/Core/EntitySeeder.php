<?php

namespace Database\Seeders\Core;

use App\Models\Core\Entity;
use App\Models\Core\Field;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('entities') as $entity) {
            $fields = $entity['fields'];

            Entity::factory()
                ->has(Field::factory()
                    ->count(count($fields))
                    ->sequence(...$fields))
                ->create([
                    'table' => $entity['table'],
                    'name' => $entity['name'],
                ]);
        }
    }
}
