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
        Schema::create('atributos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_id')->constrained('perfils')->onDelete('cascade');
            $table->string('nombre');
            $table->string('operador')->default(':=');
            $table->string('valor');
            $table->enum('tipo', ['check', 'reply'])->default('reply');
            $table->text('descripcion')->nullable();
            $table->timestamps();
            
            $table->index(['perfil_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atributos');
    }
};
