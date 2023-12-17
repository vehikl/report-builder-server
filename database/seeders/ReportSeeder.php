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
                'columns' => [
                    ['name' => 'Name', 'expression' => 'name'],
                    ['name' => 'Salary', 'expression' => 'salary'],
                    ['name' => 'Job', 'expression' => 'job.title'],
                    ['name' => 'Manager', 'expression' => 'manager.name'],
                    ['name' => 'Manager Job', 'expression' => 'manager.job.title'],
                ]
            ],
            [
                'name' => 'Employer Managers',
                'columns' => [
                    ['name' => 'Name', 'expression' => 'name'],
                    ['name' => 'Manager', 'expression' => 'manager.name'],
                ]
            ]
        ];

        foreach ($reports as $report) {
            Report::factory()
                ->has(Column::factory()
                    ->count(count($report['columns']))
                    ->sequence(...$report['columns']))
                ->create(['name' => $report['name']]);
        }

    }
}
