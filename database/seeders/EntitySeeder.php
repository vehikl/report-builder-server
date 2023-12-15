<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Entity;
use App\Models\Relation;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('entities') as $entity) {
            $attributes = $entity['attributes'];

            Entity::factory()
                ->has(Attribute::factory()
                    ->count(count($attributes))
                    ->sequence(...$attributes))
                ->create([
                    'table' => $entity['table'],
                    'name' => $entity['name'],
                ]);
        }

        foreach (config('entities') as $entity) {
            $relations = $entity['relations'];

            Relation::factory()
                ->count(count($relations))
                ->sequence(...$relations)
                ->create(['entity_table' => $entity['table']]);
        }
    }
}
