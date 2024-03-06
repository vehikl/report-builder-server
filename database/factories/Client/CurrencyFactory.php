<?php

namespace Database\Factories\Client;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client\Currency>
 */
class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->uuid(),
            'fx_to_usd' => fake()->numberBetween(0, 10),
            'fx_from_usd' => fake()->numberBetween(0, 10),
        ];
    }
}
