<?php

namespace App\Filament\Resources\FichaResource\Pages;

use App\Filament\Resources\FichaResource;
use App\Models\Radcheck;
use App\Models\Radreply;
use App\Models\Perfil;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFicha extends CreateRecord
{
    protected static string $resource = FichaResource::class;
    
    protected function afterCreate(): void
    {
        $ficha = $this->record;
        
        Radcheck::create([
            'username' => $ficha->username,
            'attribute' => 'Cleartext-Password',
            'op' => ':=',
            'value' => $ficha->password,
        ]);
    }
}
