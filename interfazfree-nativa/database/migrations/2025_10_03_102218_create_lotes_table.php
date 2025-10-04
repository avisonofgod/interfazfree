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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->integer('cantidad');
            $table->integer('longitud_password')->default(8);
            $table->enum('tipo_password', ['numerico', 'alfanumerico', 'personalizado'])->default('alfanumerico');
            $table->foreignId('perfil_id')->constrained('perfils')->onDelete('cascade');
            $table->foreignId('nas_id')->nullable()->constrained('nas')->onDelete('cascade');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
