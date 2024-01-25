<?php

namespace Database\Seeders\Structure;

use App\Models\Structure\Column;
use App\Models\Structure\Report;
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
                    ['name' => 'Name', 'expression' => new FieldExpression('name')],
                    ['name' => 'Salary', 'expression' => new FieldExpression('salary')],
                    ['name' => 'Job', 'expression' => new FieldExpression('job.title')],
                    ['name' => 'Manager', 'expression' => new FieldExpression('manager.name')],
                    ['name' => 'Manager Job', 'expression' => new FieldExpression('manager.job.title')],
                    ['name' => 'Equity Rationale', 'expression' => new FieldExpression('equity.rationale')],
                ],
            ],
            [
                'name' => 'Employee Managers',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'expression' => new FieldExpression('name')],
                    ['name' => 'Manager', 'expression' => new FieldExpression('manager.name')],
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
