<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perfil;
use App\Models\Atributo;

class AtributoSeeder extends Seeder
{
    public function run(): void
    {
        $atributosPorPerfil = [
            'Corrido' => [
                ['nombre' => 'Fall-Through', 'operador' => ':=', 'valor' => 'Yes', 'tipo' => 'check'],
                ['nombre' => 'Simultaneous-Use', 'operador' => ':=', 'valor' => '1', 'tipo' => 'check'],
                ['nombre' => 'Access-Period', 'operador' => ':=', 'valor' => '86400', 'tipo' => 'reply'],
            ],
            'Pausado' => [
                ['nombre' => 'Fall-Through', 'operador' => ':=', 'valor' => 'Yes', 'tipo' => 'check'],
                ['nombre' => 'Simultaneous-Use', 'operador' => ':=', 'valor' => '1', 'tipo' => 'check'],
                ['nombre' => 'Max-All-Session', 'operador' => ':=', 'valor' => '86400', 'tipo' => 'reply'],
            ],
            'Recurrente' => [
                ['nombre' => 'Fall-Through', 'operador' => ':=', 'valor' => 'Yes', 'tipo' => 'check'],
                ['nombre' => 'Simultaneous-Use', 'operador' => ':=', 'valor' => '1', 'tipo' => 'check'],
            ],
        ];

        foreach ($atributosPorPerfil as $nombrePerfil => $atributos) {
            $perfil = Perfil::where('nombre', $nombrePerfil)->first();
            
            if ($perfil) {
                foreach ($atributos as $atributo) {
                    Atributo::firstOrCreate(
                        [
                            'perfil_id' => $perfil->id,
                            'nombre' => $atributo['nombre'],
                            'tipo' => $atributo['tipo'],
                        ],
                        [
                            'operador' => $atributo['operador'],
                            'valor' => $atributo['valor'],
                        ]
                    );
                }
            }
        }
    }
}
