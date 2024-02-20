<?php

namespace Database\Seeders\Core;

use App\Models\Core\Column;
use App\Models\Core\Report;
use App\Utils\Expressions\FieldExpression;
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
                    ['name' => 'Name', 'position' => 0, 'expression' => new FieldExpression('name')],
                    ['name' => 'Salary', 'position' => 1, 'expression' => new FieldExpression('salary')],
                    ['name' => 'Job', 'position' => 2, 'expression' => new FieldExpression('job.title')],
                    ['name' => 'Manager', 'position' => 3, 'expression' => new FieldExpression('manager.name')],
                    ['name' => 'Manager Job', 'position' => 4, 'expression' => new FieldExpression('manager.job.title')],
                    ['name' => 'Equity Rationale', 'position' => 5, 'expression' => new FieldExpression('equity.rationale')],
                ],
            ],
            [
                'name' => 'Employee Managers',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'position' => 0, 'expression' => new FieldExpression('name')],
                    ['name' => 'Manager', 'position' => 1, 'expression' => new FieldExpression('manager.name')],
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
