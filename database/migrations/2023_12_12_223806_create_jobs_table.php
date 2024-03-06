<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id('code');
            $table->string('title');

            $table->string('family');
            $table->string('family_group');
            $table->string('ladder');
            $table->boolean('is_perf_eligible');
            $table->enum('pay_rate_type', ['Hourly', 'Salary']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
