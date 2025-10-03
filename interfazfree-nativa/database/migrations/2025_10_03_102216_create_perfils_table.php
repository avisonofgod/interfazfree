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
        Schema::create('perfils', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->enum('tipo', ['corrido', 'pausado', 'recurrente'])->default('pausado');
            $table->text('descripcion')->nullable();
            $table->string('velocidad_subida')->nullable();
            $table->string('velocidad_bajada')->nullable();
            $table->integer('tiempo_vigencia')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfils');
    }
};
