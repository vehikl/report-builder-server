<?php

namespace Database\Seeders\Structure;

use App\Models\Structure\Column;
use App\Models\Structure\Report;
use App\Utils\Expressions\AttributeExpression;
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
                    ['name' => 'Name', 'expression' => new AttributeExpression('name')],
                    ['name' => 'Salary', 'expression' => new AttributeExpression('salary')],
                    ['name' => 'Job', 'expression' => new AttributeExpression('job.title')],
                    ['name' => 'Manager', 'expression' => new AttributeExpression('manager.name')],
                    ['name' => 'Manager Job', 'expression' => new AttributeExpression('manager.job.title')],
                    ['name' => 'Equity Rationale', 'expression' => new AttributeExpression('equity.rationale')],
                ],
            ],
            [
                'name' => 'Employee Managers',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'expression' => new AttributeExpression('name')],
                    ['name' => 'Manager', 'expression' => new AttributeExpression('manager.name')],
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
