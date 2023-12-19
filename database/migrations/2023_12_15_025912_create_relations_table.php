<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained();
            $table->string('path')->nullable();
            $table->foreignId('related_entity_id')->constrained('entities');
            $table->string('name');
            $table->boolean('is_collection');
            $table->timestamps();

            $table->unique(['entity_id', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relations');
    }
};
