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
                    ['name' => 'Name', 'expression' => json_encode(['type' => 'attribute', 'value' => 'name'])],
                    ['name' => 'Salary', 'expression' => json_encode(['type' => 'attribute', 'value' => 'salary'])],
                    ['name' => 'Job', 'expression' => json_encode(['type' => 'attribute', 'value' => 'job.title'])],
                    ['name' => 'Manager', 'expression' => json_encode(['type' => 'attribute', 'value' => 'manager.name'])],
                    ['name' => 'Manager Job', 'expression' => json_encode(['type' => 'attribute', 'value' => 'manager.job.title'])],
                    ['name' => 'Equity Rationale', 'expression' => json_encode(['type' => 'attribute', 'value' => 'equity.rationale'])],
                ],
            ],
            [
                'name' => 'Employee Managers',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'expression' => json_encode(['type' => 'attribute', 'value' => 'name'])],
                    ['name' => 'Manager', 'expression' => json_encode(['type' => 'attribute', 'value' => 'manager.name'])],
                ],
            ],
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
