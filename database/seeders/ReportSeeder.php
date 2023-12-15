<?php

namespace Database\Seeders;

use App\Models\Column;
use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        Report::factory()
            ->has(Column::factory()
                ->count(3)
                ->sequence(
                    ['name' => 'Name', 'expression' => 'name'],
                    ['name' => 'Salary', 'expression' => 'salary'],
                    ['name' => 'Job', 'expression' => 'job.title'],
                    ['name' => 'Manager', 'expression' => 'manager.name'],
                    ['name' => 'Manager Job', 'expression' => 'manager.job.title'],
                ))
            ->create(['name' => 'Employees']);
    }
}
