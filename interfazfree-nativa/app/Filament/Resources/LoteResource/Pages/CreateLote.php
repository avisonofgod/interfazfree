<?php

namespace App\Filament\Resources\LoteResource\Pages;

use App\Filament\Resources\LoteResource;
use App\Models\Config;
use App\Models\Ficha;
use App\Models\Radcheck;
use App\Models\Radreply;
use App\Models\Perfil;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLote extends CreateRecord
{
    protected static string $resource = LoteResource::class;
    
    protected function afterCreate(): void
    {
        $lote = $this->record;
        $config = Config::first();
        
        $allowedChars = $config 
            ? $config->allowed_characters 
            : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        
        $longitudUsuario = $config ? $config->longitud_usuario : 4;
        $longitudPassword = $config ? $config->longitud_password : 3;
        
        for ($i = 0; $i < $lote->cantidad; $i++) {
            do {
                $username = $this->generateRandomString($allowedChars, $longitudUsuario);
            } while (Ficha::where('username', $username)->exists());
            
            $password = $this->generateRandomString($allowedChars, $longitudPassword);
            
            $ficha = Ficha::create([
                'username' => $username,
                'password' => $password,
                'estado' => 'sin_usar',
                'perfil_id' => $lote->perfil_id,
                'lote_id' => $lote->id,
            ]);
            
            Radcheck::create([
                'username' => $username,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'value' => $password,
            ]);
            
            $perfil = Perfil::find($lote->perfil_id);
            if ($perfil && $perfil->tipo === 'recurrente') {
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'WISPr-Session-Terminate-Time',
                    'op' => ':=',
                    'value' => now()->addDay()->setTime(20, 0, 0)->format('Y-m-d\TH:i:s'),
                ]);
            }
        }
    }
    
    private function generateRandomString(string $characters, int $length): string
    {
        $result = '';
        $maxIndex = strlen($characters) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $maxIndex)];
        }
        
        return $result;
    }
}
