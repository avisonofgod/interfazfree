<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PerfilSeeder::class,
            AtributoSeeder::class,
            UserSeeder::class,
        ]);
    }
}
