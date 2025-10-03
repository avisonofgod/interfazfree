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
        $atributosPorTipo = [
            'corrido' => [
                ['nombre' => 'Fall-Through', 'operador' => ':=', 'valor' => 'Yes', 'tipo' => 'check'],
                ['nombre' => 'Simultaneous-Use', 'operador' => ':=', 'valor' => '1', 'tipo' => 'check'],
                ['nombre' => 'Access-Period', 'operador' => ':=', 'valor' => '86400', 'tipo' => 'reply'],
            ],
            'pausado' => [
                ['nombre' => 'Fall-Through', 'operador' => ':=', 'valor' => 'Yes', 'tipo' => 'check'],
                ['nombre' => 'Simultaneous-Use', 'operador' => ':=', 'valor' => '1', 'tipo' => 'check'],
                ['nombre' => 'Max-All-Session', 'operador' => ':=', 'valor' => '86400', 'tipo' => 'reply'],
            ],
            'recurrente' => [
                ['nombre' => 'Fall-Through', 'operador' => ':=', 'valor' => 'Yes', 'tipo' => 'check'],
                ['nombre' => 'Simultaneous-Use', 'operador' => ':=', 'valor' => '1', 'tipo' => 'check'],
            ],
        ];

        foreach ($atributosPorTipo as $tipo => $atributos) {
            $perfil = Perfil::where('tipo', $tipo)->first();
            
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
                            'descripcion' => "Atributo {$atributo['nombre']} para perfil {$perfil->nombre}",
                        ]
                    );
                }
            }
        }
    }
}
