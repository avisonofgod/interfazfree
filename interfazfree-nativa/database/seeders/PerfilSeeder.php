<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perfil;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        Perfil::firstOrCreate(
            ['nombre' => 'Corrido'],
            [
                'tipo' => 'corrido',
                'descripcion' => 'Perfil de tiempo corrido - el tiempo corre continuamente desde el primer inicio de sesión',
                'velocidad_subida' => '2M',
                'velocidad_bajada' => '10M',
                'tiempo_vigencia' => 86400,
                'precio' => 50.00,
                'activo' => true,
            ]
        );

        Perfil::firstOrCreate(
            ['nombre' => 'Pausado'],
            [
                'tipo' => 'pausado',
                'descripcion' => 'Perfil de tiempo pausado - el tiempo solo corre cuando hay sesión activa',
                'velocidad_subida' => '2M',
                'velocidad_bajada' => '10M',
                'tiempo_vigencia' => 86400,
                'precio' => 50.00,
                'activo' => true,
            ]
        );

        Perfil::firstOrCreate(
            ['nombre' => 'Recurrente'],
            [
                'tipo' => 'recurrente',
                'descripcion' => 'Perfil recurrente - se renueva automáticamente en períodos específicos',
                'velocidad_subida' => '2M',
                'velocidad_bajada' => '10M',
                'tiempo_vigencia' => 2592000,
                'precio' => 500.00,
                'activo' => true,
            ]
        );
    }
}
