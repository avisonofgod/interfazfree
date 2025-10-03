<?php

namespace Database\Factories;

use App\Models\Perfil;
use App\Models\Nas;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(3, true),
            'cantidad' => $this->faker->numberBetween(10, 100),
            'longitud_password' => 8,
            'tipo_password' => 'alfanumerico',
            'perfil_id' => Perfil::factory(),
            'nas_id' => Nas::factory(),
        ];
    }
}
