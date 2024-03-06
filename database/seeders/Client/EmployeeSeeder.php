<?php

namespace Database\Seeders\Client;

use App\Models\Client\Currency;
use App\Models\Client\Employee;
use App\Models\Client\Job;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = Currency::factory()->count(3)->sequence(
            ['code' => 'USD', 'fx_to_usd' => 1.0, 'fx_from_usd' => 1.0],
            ['code' => 'CAD', 'fx_to_usd' => 0.8, 'fx_from_usd' => 0.2],
            ['code' => 'BRL', 'fx_to_usd' => 0.2, 'fx_from_usd' => 0.8],
        )->create();

        $managerJob = Job::factory()->create(['title' => 'Manager']);

        $managers = Employee::factory()
            ->count(2)
            ->for($currencies->random())
            ->create(['job_code' => $managerJob]);

        Employee::factory()->count(4)
            ->for($currencies->random())
            ->sequence(
                ['manager_id' => $managers->get(0)],
                [
                    'manager_id' => $managers->get(1),
                    'equity_amount' => null,
                    'equity_rationale' => null,
                ],
            )->create();

        Employee::factory()->count(100)
            ->sequence(...$currencies->map(fn (Currency $currency) => ['currency_code' => $currency])->all())
            ->create([
                'manager_id' => Employee::factory()->for($currencies->random()),
            ]);
    }
}
