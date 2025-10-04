<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
        
        Schema::table('nas', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
        
        Schema::table('fichas', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
        
        Schema::table('perfils', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
        
        Schema::table('atributos', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
        });
        
        Schema::table('nas', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
        });
        
        Schema::table('fichas', function (Blueprint $table) {
            $table->text('observaciones')->nullable();
        });
        
        Schema::table('perfils', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
        });
        
        Schema::table('atributos', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
        });
    }
};
