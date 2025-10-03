<?php

namespace Database\Factories;

use App\Models\Perfil;
use App\Models\Lote;
use Illuminate\Database\Eloquent\Factories\Factory;

class FichaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'password' => $this->faker->password(8),
            'estado' => 'sin_usar',
            'perfil_id' => Perfil::factory(),
            'lote_id' => null,
        ];
    }
}
