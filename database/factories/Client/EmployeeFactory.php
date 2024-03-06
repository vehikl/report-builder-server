<?php

namespace Database\Factories\Client;

use App\Models\Client\Currency;
use App\Models\Client\Employee;
use App\Models\Client\Enums\EmployeeProgram;
use App\Models\Client\Enums\EmployeeStatus;
use App\Models\Client\Job;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    public const COMPANY_FULLS = [
        '1001 Uber Technologies, Inc.',
        '1200 Uber Canada Inc.',
        '2009 Uber Czech Republic Technology s.r.o.',
        '2017 Uber Italy S.R.L.',
        '4003 Uber Do Brasil Tecnologia LTDA',
    ];

    public function definition(): array
    {
        $hireDate = fake()->dateTimeThisDecade();
        $status = Arr::random(EmployeeStatus::cases());
        $terminationDate = $status === EmployeeStatus::Terminated ? $hireDate->add(new DateInterval('P1M')) : null;
        $countryCode = fake()->countryCode();

        return [
            'display_name' => fake()->name(),
            'email' => fake()->email(),
            'hire_date' => $hireDate,
            'company_full' => Arr::random(self::COMPANY_FULLS),
            'termination_date' => $terminationDate,
            'status' => $status,
            'role_type' => Arr::random(EmployeeProgram::cases()),
            'salary' => fake()->randomFloat(2, 70000, 150000),
            'algo_salary' => fake()->randomFloat(2, 70000, 150000),
            'new_salary' => fake()->randomFloat(2, 70000, 150000),
            'location' => fake()->country(),
            'city_tier' => $countryCode.' '.fake()->numberBetween(1000, 9999),
            'region' => $countryCode.' '.'Region',
            'country' => $countryCode,
            'country_city' => $countryCode.' '.fake()->city(),
            'currency_code' => fn () => Currency::factory(),
            'reports_to' => [],
            'manager_id' => null,
            'job_code' => fn () => Job::factory(),
            'promo_job_code' => fn () => Job::factory(),
            'new_job_code' => fn () => Job::factory(),
            'equity_amount' => fake()->randomFloat(2, 10000, 50000),
            'equity_rationale' => fake()->sentence(4),
        ];
    }
}
