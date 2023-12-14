<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Employee;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $managers = Employee::factory()
            ->count(2)
            ->for(Job::factory()->sequence(['title' => 'Manager']))
            ->create();

        Employee::factory()->count(4)->sequence(
            ['manager_id' => $managers->get(0)],
            ['manager_id' => $managers->get(1)],
        )
        ->create();

        $this->call(EntitySeeder::class);
    }
}
