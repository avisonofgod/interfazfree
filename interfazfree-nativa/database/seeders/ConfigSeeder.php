<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        Config::firstOrCreate(
            ['id' => 1],
            [
                'allowed_characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
                'encryption_type' => 'cleartext',
                'longitud_usuario' => 4,
                'longitud_password' => 3,
            ]
        );
    }
}
