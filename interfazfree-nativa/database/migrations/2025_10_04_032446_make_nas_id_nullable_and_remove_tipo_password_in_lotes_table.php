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
        Schema::table('lotes', function (Blueprint $table) {
            if (Schema::hasColumn('lotes', 'tipo_password')) {
                $table->dropColumn('tipo_password');
            }
        });
        
        Schema::table('lotes', function (Blueprint $table) {
            $table->foreignId('nas_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->foreignId('nas_id')->nullable(false)->change();
        });
        
        Schema::table('lotes', function (Blueprint $table) {
            $table->enum('tipo_password', ['numerico', 'alfanumerico', 'personalizado'])
                ->default('alfanumerico')
                ->after('longitud_password');
        });
    }
};
