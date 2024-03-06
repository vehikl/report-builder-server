<?php

namespace Database\Factories\Client;

use App\Models\Client\Enums\PayRateType;
use App\Models\Client\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    public const FAMILY_GROUPS = [
        'Business Operations' => [
            'LCR' => ['LCR'],
            'Local Operations' => ['Business Analytics', 'Data Analytics'],
            'Product Operations' => ['Product Operations'],
        ],
        'Corporate' => [
            'Business Development' => ['Business Development', 'Business Development Management'],
            'Finance' => ['Buyer Operations', 'Corporate Development'],
        ],
        'Customer Service' => [
            'Customer Enablement' => ['Service Team Lead (QC)'],
            'Customer Production' => ['Safety Investigations Team Lead', 'Service Support (NV)'],
        ],
        'Marketing Group' => [
            'Marketing Strategy' => ['Creative'],
        ],
        'Sales' => [
            'Sales Incentive Plan' => ['Account Executive, Eats Corporate (Sales Plan)', 'Account Executive, Eats Franchise'],
            'Sales Non-Incentive Plan' => ['Sales Manager (Annual Plan)'],
        ],
        'Tech' => [
            'Engineer' => ['Autonomy Hardware Engineering', 'Technical Lead Manager, Systems Engineer'],
            'Product Development and Operations' => ['New Mobility Incubator'],
            'Science' => ['Data Scientist', 'Manager, Data Scientist'],
        ],
    ];

    public function definition(): array
    {
        $familyGroup = Arr::random(array_keys(self::FAMILY_GROUPS));
        $family = Arr::random(array_keys(self::FAMILY_GROUPS[$familyGroup]));
        $ladder = Arr::random(self::FAMILY_GROUPS[$familyGroup][$family]);

        return [
            'title' => fake()->jobTitle(),
            'family' => $family,
            'family_group' => $familyGroup,
            'ladder' => $ladder,
            'is_perf_eligible' => fake()->boolean(),
            'pay_rate_type' => Arr::random([PayRateType::Salary, PayRateType::Hourly]),
        ];
    }
}
