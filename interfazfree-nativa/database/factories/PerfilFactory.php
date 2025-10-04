<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PerfilFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(2, true),
            'tipo' => $this->faker->randomElement(['corrido', 'pausado', 'recurrente']),
            'velocidad_subida' => '10M',
            'velocidad_bajada' => '20M',
            'tiempo_vigencia' => 30,
            'precio' => $this->faker->randomFloat(2, 50, 500),
            'activo' => true,
        ];
    }
}
