<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Client\EmployeeSeeder;
use Database\Seeders\Core\EntitySeeder;
use Database\Seeders\Core\ReportSeeder;
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
