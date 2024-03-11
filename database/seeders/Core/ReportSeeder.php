<?php

namespace Database\Seeders\Core;

use App\Models\Core\Column;
use App\Models\Core\Report;
use App\Utils\Expressions\FieldExpression;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $redFlagColumns = [
            ['name' => 'Employee ID', 'position' => 0, 'expression' => new FieldExpression('id')],
            ['name' => 'Employee Name', 'position' => 1, 'expression' => new FieldExpression('display_name')],
            ['name' => 'Program as of Jan 1, 2024', 'position' => 2, 'expression' => new FieldExpression('program')],
            ['name' => 'Hire Date', 'position' => 3, 'expression' => new FieldExpression('hire_date')],
            ['name' => 'Termed', 'position' => 4, 'expression' => new FieldExpression('is_termed')],
            ['name' => 'ELT', 'position' => 5, 'expression' => new FieldExpression('manager.manager.id')],
            ['name' => 'ELT + 1', 'position' => 6, 'expression' => new FieldExpression('manager.manager.manager.id')],
            ['name' => 'ELT + 2', 'position' => 7, 'expression' => new FieldExpression('manager.manager.manager.manager.id')],
            ['name' => 'ELT + 3', 'position' => 8, 'expression' => new FieldExpression('manager.manager.manager.manager.manager.id')],
            ['name' => 'Manager ID', 'position' => 9, 'expression' => new FieldExpression('manager.id')],
            ['name' => "Worker's Manager", 'position' => 10, 'expression' => new FieldExpression('manager.display_name')],
            ['name' => 'Region', 'position' => 11, 'expression' => new FieldExpression('location.region')],
            ['name' => 'Country', 'position' => 12, 'expression' => new FieldExpression('location.country')],
            ['name' => 'Location', 'position' => 13, 'expression' => new FieldExpression('location.name')],
            ['name' => 'Country City', 'position' => 14, 'expression' => new FieldExpression('location.country_city')],
            ['name' => 'Geo Code', 'position' => 15, 'expression' => new FieldExpression('location.city_tier')],
            ['name' => 'Local Curr.', 'position' => 16, 'expression' => new FieldExpression('currency_code')],
            ['name' => 'FX Rate to USD', 'position' => 17, 'expression' => new FieldExpression('currency_fx_to_usd')],
            ['name' => '2024-02-29 Job Code', 'position' => 18, 'expression' => new FieldExpression('job.code')],
            ['name' => '2024-02-29 Job Profile', 'position' => 19, 'expression' => new FieldExpression('job.title')],
            ['name' => '2024-02-29 Job Family', 'position' => 20, 'expression' => new FieldExpression('job.family')],
            ['name' => '2024-02-29 Job Family Group', 'position' => 21, 'expression' => new FieldExpression('job.family_group')],
            ['name' => 'Promo Confirmed', 'position' => 22, 'expression' => new FieldExpression('has_promotion')],
            ['name' => 'Promo: Hourly to Salary', 'position' => 23, 'expression' => new FieldExpression('is_promo_hourly_to_salary')],
            ['name' => 'Promo: Salary to Hourly', 'position' => 24, 'expression' => new FieldExpression('is_promo_salary_to_hourly')],
            ['name' => '2024-03-01 Job Code', 'position' => 25, 'expression' => new FieldExpression('new_job.code')],
            ['name' => '2024-03-01 Job Profile', 'position' => 26, 'expression' => new FieldExpression('new_job.title')],
            ['name' => '2024-03-01 Job Ladder', 'position' => 27, 'expression' => new FieldExpression('new_job.ladder')],
            ['name' => '2024-03-01 Job Family', 'position' => 28, 'expression' => new FieldExpression('new_job.family')],
            ['name' => '2024-03-01 Job Family Group', 'position' => 29, 'expression' => new FieldExpression('new_job.family_group')],
            ['name' => 'Current Salary', 'position' => 30, 'expression' => new FieldExpression('salary')],
            ['name' => 'Current Salary (USD)', 'position' => 31, 'expression' => new FieldExpression('salary_usd')],
            ['name' => 'Base Salary Change (LC)', 'position' => 32, 'expression' => new FieldExpression('salary_increase_amount')],
            ['name' => 'Algo Base Salary (USD)', 'position' => 33, 'expression' => new FieldExpression('algo_salary_usd')],
            ['name' => 'Final Base Salary (USD)', 'position' => 34, 'expression' => new FieldExpression('new_salary_usd')],
            ['name' => 'Base Salary Change (USD)', 'position' => 35, 'expression' => new FieldExpression('salary_increase_amount_usd')],
            ['name' => 'Base Salary % Change', 'position' => 36, 'expression' => new FieldExpression('salary_increase_percent')],
        ];

        $biggieColumns = [];

        for ($i = 0; $i < 8; $i++) {
            $biggieColumns = array_merge($biggieColumns, $redFlagColumns);
        }

        $biggieColumns = Arr::map($biggieColumns, fn (array $column, $i) => [...$column, 'position'=> $i]);



        $reports = [
            [
                'name' => 'Employees',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'position' => 0, 'expression' => new FieldExpression('display_name')],
                    ['name' => 'Salary', 'position' => 1, 'expression' => new FieldExpression('salary')],
                    ['name' => 'Job', 'position' => 2, 'expression' => new FieldExpression('job.title')],
                    ['name' => 'Manager', 'position' => 3, 'expression' => new FieldExpression('manager.display_name')],
                    ['name' => 'Manager Job', 'position' => 4, 'expression' => new FieldExpression('manager.job.title')],
                    ['name' => 'Equity Rationale', 'position' => 5, 'expression' => new FieldExpression('equity.rationale')],
                ],
            ],
            [
                'name' => 'Employee Managers',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Name', 'position' => 0, 'expression' => new FieldExpression('display_name')],
                    ['name' => 'Manager', 'position' => 1, 'expression' => new FieldExpression('manager.display_name')],
                ],
            ],
            [
                'name' => 'Red Flag',
                'entity_id' => 2,
                'columns' => [
                    ['name' => 'Employee ID', 'position' => 0, 'expression' => new FieldExpression('id')],
                    ['name' => 'Employee Name', 'position' => 1, 'expression' => new FieldExpression('display_name')],
                    ['name' => 'Program as of Jan 1, 2024', 'position' => 2, 'expression' => new FieldExpression('program')],
                    ['name' => 'Hire Date', 'position' => 3, 'expression' => new FieldExpression('hire_date')],
                    ['name' => 'Termed', 'position' => 4, 'expression' => new FieldExpression('is_termed')],
                    ['name' => 'ELT', 'position' => 5, 'expression' => new FieldExpression('manager.manager.id')],
                    ['name' => 'ELT + 1', 'position' => 6, 'expression' => new FieldExpression('manager.manager.manager.id')],
                    ['name' => 'ELT + 2', 'position' => 7, 'expression' => new FieldExpression('manager.manager.manager.manager.id')],
                    ['name' => 'ELT + 3', 'position' => 8, 'expression' => new FieldExpression('manager.manager.manager.manager.manager.id')],
                    ['name' => 'Manager ID', 'position' => 9, 'expression' => new FieldExpression('manager.id')],
                    ['name' => "Worker's Manager", 'position' => 10, 'expression' => new FieldExpression('manager.display_name')],
                    ['name' => 'Region', 'position' => 11, 'expression' => new FieldExpression('location.region')],
                    ['name' => 'Country', 'position' => 12, 'expression' => new FieldExpression('location.country')],
                    ['name' => 'Location', 'position' => 13, 'expression' => new FieldExpression('location.name')],
                    ['name' => 'Country City', 'position' => 14, 'expression' => new FieldExpression('location.country_city')],
                    ['name' => 'Geo Code', 'position' => 15, 'expression' => new FieldExpression('location.city_tier')],
                    ['name' => 'Local Curr.', 'position' => 16, 'expression' => new FieldExpression('currency_code')],
                    ['name' => 'FX Rate to USD', 'position' => 17, 'expression' => new FieldExpression('currency_fx_to_usd')],
                    ['name' => '2024-02-29 Job Code', 'position' => 18, 'expression' => new FieldExpression('job.code')],
                    ['name' => '2024-02-29 Job Profile', 'position' => 19, 'expression' => new FieldExpression('job.title')],
                    ['name' => '2024-02-29 Job Family', 'position' => 20, 'expression' => new FieldExpression('job.family')],
                    ['name' => '2024-02-29 Job Family Group', 'position' => 21, 'expression' => new FieldExpression('job.family_group')],
                    ['name' => 'Promo Confirmed', 'position' => 22, 'expression' => new FieldExpression('has_promotion')],
                    ['name' => 'Promo: Hourly to Salary', 'position' => 23, 'expression' => new FieldExpression('is_promo_hourly_to_salary')],
                    ['name' => 'Promo: Salary to Hourly', 'position' => 24, 'expression' => new FieldExpression('is_promo_salary_to_hourly')],
                    ['name' => '2024-03-01 Job Code', 'position' => 25, 'expression' => new FieldExpression('new_job.code')],
                    ['name' => '2024-03-01 Job Profile', 'position' => 26, 'expression' => new FieldExpression('new_job.title')],
                    ['name' => '2024-03-01 Job Ladder', 'position' => 27, 'expression' => new FieldExpression('new_job.ladder')],
                    ['name' => '2024-03-01 Job Family', 'position' => 28, 'expression' => new FieldExpression('new_job.family')],
                    ['name' => '2024-03-01 Job Family Group', 'position' => 29, 'expression' => new FieldExpression('new_job.family_group')],
                    ['name' => 'Current Salary', 'position' => 30, 'expression' => new FieldExpression('salary')],
                    ['name' => 'Current Salary (USD)', 'position' => 31, 'expression' => new FieldExpression('salary_usd')],
                    ['name' => 'Base Salary Change (LC)', 'position' => 32, 'expression' => new FieldExpression('salary_increase_amount')],
                    ['name' => 'Algo Base Salary (USD)', 'position' => 33, 'expression' => new FieldExpression('algo_salary_usd')],
                    ['name' => 'Final Base Salary (USD)', 'position' => 34, 'expression' => new FieldExpression('new_salary_usd')],
                    ['name' => 'Base Salary Change (USD)', 'position' => 35, 'expression' => new FieldExpression('salary_increase_amount_usd')],
                    ['name' => 'Base Salary % Change', 'position' => 36, 'expression' => new FieldExpression('salary_increase_percent')],
                ],
            ],
            [
                'name' => 'Biggie',
                'entity_id' => 2,
                'columns' => $biggieColumns,
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
