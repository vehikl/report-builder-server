<?php

namespace Database\Factories\Data;

use App\Models\Data\Employee;
use App\Models\Data\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'salary' => fake()->randomFloat(2, 70000, 150000),
            'bonus' => fake()->randomFloat(2, 10000, 20000),
            'manager_id' => null,
            'job_code' => fn () => Job::factory(),
            'equity_amount' => fake()->randomFloat(2, 10000, 50000),
            'equity_rationale' => fake()->sentence(4),
        ];
    }
}
