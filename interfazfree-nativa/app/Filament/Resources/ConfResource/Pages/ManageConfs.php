<?php

namespace App\Filament\Resources\ConfResource\Pages;

use App\Filament\Resources\ConfResource;
use App\Models\Config;
use Filament\Resources\Pages\Page;

class ManageConfs extends Page
{
    protected static string $resource = ConfResource::class;

    protected static string $view = 'filament.resources.conf-resource.pages.manage-confs';

    public function getConfigurations(): array
    {
        $config = Config::first();
        
        return [
            [
                'label' => 'Caracteres Permitidos',
                'value' => $config?->allowed_characters ?? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            ],
            [
                'label' => 'Tipo de Cifrado',
                'value' => $config?->encryption_type ?? 'cleartext',
            ],
            [
                'label' => 'Longitud de Usuario',
                'value' => $config?->longitud_usuario ?? 4,
            ],
            [
                'label' => 'Longitud de Contraseña',
                'value' => $config?->longitud_password ?? 3,
            ],
        ];
    }
}
