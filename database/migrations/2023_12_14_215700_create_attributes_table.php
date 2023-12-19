<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained();
            $table->string('path');
            $table->string('name');
            $table->timestamps();

            $table->unique(['entity_id', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
