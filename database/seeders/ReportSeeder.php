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
                    ['name' => 'Name', 'expression' => '5:name'],
                    ['name' => 'Salary', 'expression' => '6:salary'],
                    ['name' => 'Job', 'expression' => '10:job,2:title'],
                    ['name' => 'Manager', 'expression' => '11:manager,5:name'],
                    ['name' => 'Manager Job', 'expression' => '11:manager,10:job,2:title'],
                    ['name' => 'Equity Rationale', 'expression' => '13:,15:equity_rationale'],
                ]
            ],
            [
                'name' => 'Employee Managers',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'expression' => '5:name'],
                    ['name' => 'Manager', 'expression' => '11:manager,5:name'],
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
