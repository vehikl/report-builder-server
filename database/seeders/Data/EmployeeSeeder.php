<?php

namespace Database\Seeders\Data;

use App\Models\Data\Employee;
use App\Models\Data\Job;
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
    }
}
