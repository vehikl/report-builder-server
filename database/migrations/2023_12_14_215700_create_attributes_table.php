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
            $table->string('identifier');
            $table->string('path')->nullable();
            $table->string('name');
            $table->string('type');
            $table->timestamps();

            $table->unique(['entity_id', 'identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
