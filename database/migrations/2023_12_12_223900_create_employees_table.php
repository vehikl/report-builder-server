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
            $table->decimal('salary', 9);
            $table->decimal('bonus', 9);
            $table->foreignId('job_code')->constrained('jobs', 'code');
            $table->foreignId('manager_id')->nullable()->constrained('employees');
            $table->json('reports_to')->default(DB::raw('(JSON_ARRAY())'));

            $table->decimal('equity_amount', 9)->nullable();
            $table->string('equity_rationale')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
