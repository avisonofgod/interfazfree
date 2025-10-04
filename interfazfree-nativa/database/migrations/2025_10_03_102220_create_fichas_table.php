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
        Schema::create('fichas', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('estado', ['sin_usar', 'activa', 'caducada'])->default('sin_usar');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_expiracion')->nullable();
            $table->foreignId('perfil_id')->constrained('perfils')->onDelete('cascade');
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('estado');
            $table->index('fecha_expiracion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichas');
    }
};
