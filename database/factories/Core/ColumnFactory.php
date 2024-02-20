<?php

namespace Database\Factories\Core;

use App\Models\Core\Column;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Column>
 */
class ColumnFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'expression' => fake()->word.'.'.fake()->word,
            'position' => 0,
        ];
    }
}
