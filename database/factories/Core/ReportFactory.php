<?php

namespace Database\Factories\Core;

use App\Models\Core\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->sentence(2)];
    }
}
