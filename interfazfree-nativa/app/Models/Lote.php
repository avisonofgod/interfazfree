<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'cantidad',
        'longitud_password',
        'longitud_usuario',
        'perfil_id',
        'nas_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'longitud_password' => 'integer',
        'longitud_usuario' => 'integer',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }

    public function nas()
    {
        return $this->belongsTo(Nas::class);
    }

    public function fichas()
    {
        return $this->hasMany(Ficha::class);
    }
}
