<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('relations', function (Blueprint $table) {
            $table->id();
            $table->string('entity_table');
            $table->string('accessor');
            $table->string('related_entity_table');
            $table->string('name');
            $table->boolean('is_collection');
            $table->timestamps();

            $table->unique(['entity_table', 'accessor']);
            $table->foreign('entity_table')->references('table')->on('entities');
            $table->foreign('related_entity_table')->references('table')->on('entities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relations');
    }
};
