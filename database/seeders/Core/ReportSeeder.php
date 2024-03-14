<?php

namespace Database\Seeders\Core;

use App\Models\Core\Column;
use App\Models\Core\Report;
use App\Utils\Expressions\FieldExpression;
use App\Utils\Format\Format;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $redFlagColumns = [
            ['name' => 'Employee ID', 'expression' => new FieldExpression('id')],
            ['name' => 'Employee Name', 'expression' => new FieldExpression('display_name')],
            ['name' => 'Program as of Jan 1, 2024', 'expression' => new FieldExpression('program')],
            ['name' => 'Hire Date', 'expression' => new FieldExpression('hire_date')],
            ['name' => 'Termed', 'expression' => new FieldExpression('is_termed'), 'format' => Format::YesNo->value],
            ['name' => 'ELT', 'expression' => new FieldExpression('manager.manager.id')],
            ['name' => 'ELT + 1', 'expression' => new FieldExpression('manager.manager.manager.id')],
            ['name' => 'ELT + 2', 'expression' => new FieldExpression('manager.manager.manager.manager.id')],
            ['name' => 'ELT + 3', 'expression' => new FieldExpression('manager.manager.manager.manager.manager.id')],
            ['name' => 'Manager ID', 'expression' => new FieldExpression('manager.id')],
            ['name' => "Worker's Manager", 'expression' => new FieldExpression('manager.display_name')],
            ['name' => 'Region', 'expression' => new FieldExpression('location.region')],
            ['name' => 'Country', 'expression' => new FieldExpression('location.country')],
            ['name' => 'Location', 'expression' => new FieldExpression('location.name')],
            ['name' => 'Country City', 'expression' => new FieldExpression('location.country_city')],
            ['name' => 'Geo Code', 'expression' => new FieldExpression('location.city_tier')],
            ['name' => 'Local Curr.', 'expression' => new FieldExpression('currency_code')],
            ['name' => 'FX Rate to USD', 'expression' => new FieldExpression('currency_fx_to_usd')],
            ['name' => '2024-02-29 Job Code', 'expression' => new FieldExpression('job.code')],
            ['name' => '2024-02-29 Job Profile', 'expression' => new FieldExpression('job.title')],
            ['name' => '2024-02-29 Job Family', 'expression' => new FieldExpression('job.family')],
            ['name' => '2024-02-29 Job Family Group', 'expression' => new FieldExpression('job.family_group')],
            ['name' => 'Promo Confirmed', 'expression' => new FieldExpression('has_promotion')],
            ['name' => 'Promo: Hourly to Salary', 'expression' => new FieldExpression('is_promo_hourly_to_salary')],
            ['name' => 'Promo: Salary to Hourly', 'expression' => new FieldExpression('is_promo_salary_to_hourly')],
            ['name' => '2024-03-01 Job Code', 'expression' => new FieldExpression('new_job.code')],
            ['name' => '2024-03-01 Job Profile', 'expression' => new FieldExpression('new_job.title')],
            ['name' => '2024-03-01 Job Ladder', 'expression' => new FieldExpression('new_job.ladder')],
            ['name' => '2024-03-01 Job Family', 'expression' => new FieldExpression('new_job.family')],
            ['name' => '2024-03-01 Job Family Group', 'expression' => new FieldExpression('new_job.family_group')],
            ['name' => 'Current Salary', 'expression' => new FieldExpression('salary'), 'format' => Format::NumberZeroDecimal->value],
            ['name' => 'Current Salary (USD)', 'expression' => new FieldExpression('salary_usd')],
            ['name' => 'Base Salary Change (LC)', 'expression' => new FieldExpression('salary_increase_amount')],
            ['name' => 'Algo Base Salary (USD)', 'expression' => new FieldExpression('algo_salary_usd')],
            ['name' => 'Final Base Salary (USD)', 'expression' => new FieldExpression('new_salary_usd')],
            ['name' => 'Base Salary Change (USD)', 'expression' => new FieldExpression('salary_increase_amount_usd')],
            ['name' => 'Base Salary % Change', 'expression' => new FieldExpression('salary_increase_percent'), 'format' => Format::NumberTwoDecimals->value],
        ];

        $biggieColumns = [];

        for ($i = 0; $i < 7; $i++) {
            $biggieColumns = array_merge($biggieColumns, $redFlagColumns);
        }

        $biggieColumns = Arr::map($biggieColumns, fn (array $column, $i) => [...$column, 'position' => $i]);

        $reports = [
            [
                'name' => 'Employees',
                'entity_id' => 'employee',
                'columns' => [
                    ['name' => 'Name', 'expression' => new FieldExpression('display_name')],
                    ['name' => 'Salary', 'expression' => new FieldExpression('salary')],
                    ['name' => 'Job', 'expression' => new FieldExpression('job.title')],
                    ['name' => 'Manager', 'expression' => new FieldExpression('manager.display_name')],
                    ['name' => 'Manager Job', 'expression' => new FieldExpression('manager.job.title')],
                    ['name' => 'Equity Rationale', 'expression' => new FieldExpression('equity.rationale')],
                ],
            ],
            [
                'name' => 'Employee Managers',
                'entity_id' => 'employee',
                'columns' => [
                    ['name' => 'Name', 'expression' => new FieldExpression('display_name')],
                    ['name' => 'Manager', 'expression' => new FieldExpression('manager.display_name')],
                ],
            ],
            [
                'name' => 'Red Flag',
                'entity_id' => 'employee',
                'columns' => $redFlagColumns,
            ],
            [
                'name' => 'Biggie',
                'entity_id' => 'employee',
                'columns' => $biggieColumns,
            ],
        ];

        foreach ($reports as $report) {
            $reportModel = Report::factory()
                ->create([
                    'name' => $report['name'],
                    'entity_id' => $report['entity_id'],
                ]);

            foreach ($report['columns'] as $i => $column) {
                Column::factory()->create([
                    'report_id' => $reportModel,
                    'position' => $i,
                    ...$column,
                ]);
            }
        }
    }
}
