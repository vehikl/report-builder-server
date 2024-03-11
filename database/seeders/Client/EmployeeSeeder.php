<?php

namespace Database\Seeders\Client;

use App\Models\Client\Currency;
use App\Models\Client\Employee;
use App\Models\Client\Job;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employeeCount = 20000;

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

        $output = new OutputStyle(new StringInput(''), new ConsoleOutput());

        $bar = $output->createProgressBar($employeeCount);

        for ($j = 0; $j < $employeeCount; $j++) {
            Employee::factory()
                ->for($currencies->random())
                ->create([
                    'manager_id' => Employee::factory()->for($currencies->random()),
                ]);

            $bar->advance();
        }

        $bar->finish();
        $output->writeln('');
    }
}
