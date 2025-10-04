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
        Schema::table('atributos', function (Blueprint $table) {
            $table->string('tipo')->default('reply')->change();
        });
    }

    public function down(): void
    {
        Schema::table('atributos', function (Blueprint $table) {
            $table->enum('tipo', ['check', 'reply'])->default('reply')->change();
        });
    }
};
