<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cantidad',
        'longitud_password',
        'tipo_password',
        'perfil_id',
        'nas_id',
        'descripcion',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'longitud_password' => 'integer',
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
