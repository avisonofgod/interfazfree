<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nas extends Model
{
    use HasFactory;

    protected $table = 'nas';

    protected $fillable = [
        'nombre',
        'shortname',
        'tipo',
        'descripcion',
        'ip',
        'puerto',
        'secreto',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'puerto' => 'integer',
    ];

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }
}
