<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NasFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'shortname' => $this->faker->slug(2),
            'tipo' => $this->faker->randomElement(['mikrotik', 'opnsense', 'other']),
            'ip' => $this->faker->ipv4(),
            'puerto' => 1812,
            'secreto' => $this->faker->password(12),
            'activo' => true,
        ];
    }
}
