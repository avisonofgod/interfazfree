<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'velocidad_subida',
        'velocidad_bajada',
        'tiempo_vigencia',
        'precio',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'precio' => 'decimal:2',
    ];

    public function fichas()
    {
        return $this->hasMany(Ficha::class);
    }

    public function atributos()
    {
        return $this->hasMany(Atributo::class);
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }
}
