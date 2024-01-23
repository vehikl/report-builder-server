<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Data\EmployeeSeeder;
use Database\Seeders\Structure\EntitySeeder;
use Database\Seeders\Structure\ReportSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(EmployeeSeeder::class);
        $this->call(EntitySeeder::class);
        $this->call(ReportSeeder::class);
    }
}
