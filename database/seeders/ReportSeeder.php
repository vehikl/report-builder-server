<?php

namespace Database\Seeders;

use App\Models\Column;
use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reports = [
            [
                'name' => 'Employees',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'expression' => '4:name'],
                    ['name' => 'Salary', 'expression' => '5:salary'],
                    ['name' => 'Job', 'expression' => '2:job,2:title'],
                    ['name' => 'Manager', 'expression' => '3:manager,4:name'],
                    ['name' => 'Manager Job', 'expression' => '3:manager,2:job,2:title'],
                    ['name' => 'Equity Rationale', 'expression' => '5:,10:equity_rationale'],
                ]
            ],
            [
                'name' => 'Employee Managers',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'expression' => '4:name'],
                    ['name' => 'Manager', 'expression' => '3:manager,4:name'],
                ]
            ]
        ];

        foreach ($reports as $report) {
            Report::factory()
                ->has(Column::factory()
                    ->count(count($report['columns']))
                    ->sequence(...$report['columns']))
                ->create([
                    'name' => $report['name'],
                    'entity_id' => $report['entity_id'],
                ]);
        }

    }
}
