<?php

namespace Database\Seeders\Client;

use App\Models\Client\Employee;
use App\Models\Client\Job;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $managerJob = Job::factory()->create(['title' => 'Manager']);

        $managers = Employee::factory()
            ->count(2)
            ->create(['job_code' => $managerJob]);

        Employee::factory()->count(4)->sequence(
            ['manager_id' => $managers->get(0)],
            [
                'manager_id' => $managers->get(1),
                'equity_amount' => null,
                'equity_rationale' => null,
            ],
        )
            ->create();

        Employee::factory()->count(20000)->create(['manager_id' => Employee::factory()]);
    }
}
