<?php

namespace Database\Seeders\Client;

use App\Models\Client\Currency;
use App\Models\Client\Employee;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = Currency::factory()->count(3)->sequence(
            ['code' => 'USD', 'fx_to_usd' => 1.0, 'fx_from_usd' => 1.0],
            ['code' => 'CAD', 'fx_to_usd' => 0.8, 'fx_from_usd' => 0.2],
            ['code' => 'BRL', 'fx_to_usd' => 0.2, 'fx_from_usd' => 0.8],
        )->create();

        $this->createEmployees($currencies, 30000);
    }

    /** @param  Collection<Currency>  $currencies */
    public function createEmployees(Collection $currencies, int $total): void
    {

        $managers = [Employee::factory()->for($currencies->random())->create()];
        $count = 1;
        $tier = 2;

        $output = new OutputStyle(new StringInput(''), new ConsoleOutput());
        $bar = $output->createProgressBar($total);
        $bar->advance();

        while (true) {
            $employees = [];
            foreach ($managers as $manager) {
                for ($i = 0; $i < $tier; $i++) {
                    $employee = Employee::factory()
                        ->for($currencies->random())
                        ->create([
                            'manager_id' => $manager,
                            'reports_to' => [$manager->id, ...$manager->reports_to],
                        ]);

                    $employees[] = $employee;

                    $bar->advance();

                    if ($count++ >= $total) {
                        $bar->finish();
                        $output->writeln('');

                        return;
                    }
                }
            }
            $managers = $employees;
            $tier++;
        }
    }
}
