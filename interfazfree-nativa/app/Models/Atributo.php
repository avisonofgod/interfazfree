<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $fillable = [
        'perfil_id',
        'nombre',
        'operador',
        'valor',
        'tipo',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }
}
