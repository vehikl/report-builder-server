<?php

namespace Database\Seeders\Structure;

use App\Models\Structure\Field;
use App\Models\Structure\Entity;
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
