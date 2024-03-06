<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->string('email');
            $table->date('hire_date');
            $table->string('company_full');
            $table->date('termination_date')->nullable();
            $table->enum('status', ['Active', 'Contractor', 'On Leave', 'Terminated']);
            $table->enum('role_type', ['Core', 'Eng', 'S Ladder']);
            $table->decimal('salary', 9);
            $table->decimal('algo_salary', 9);
            $table->decimal('new_salary', 9);

            $table->string('location');
            $table->string('city_tier');
            $table->string('region');
            $table->string('country_city');
            $table->string('country');

            $table->string('currency_code')->references('code')->on('currencies');

            $table->json('reports_to')->default(DB::raw('(JSON_ARRAY())'));
            $table->foreignId('manager_id')->nullable()->constrained('employees');

            $table->foreignId('job_code')->constrained('jobs', 'code');
            $table->foreignId('promo_job_code')->nullable()->constrained('jobs', 'code');
            $table->foreignId('new_job_code')->nullable()->constrained('jobs', 'code');

            $table->timestamps();

            $table->decimal('equity_amount', 9)->nullable();
            $table->string('equity_rationale')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
