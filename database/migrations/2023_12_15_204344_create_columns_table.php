<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('columns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('report_id')->constrained();
            $table->integer('position');
            $table->enum('format', ['General', 'YesNo', 'NumberZeroDecimal', 'NumberTwoDecimals'])->default('General');
            $table->json('expression');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('columns');
    }
};
