<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $fillable = [
        'allowed_characters',
        'encryption_type',
        'longitud_usuario',
        'longitud_password',
    ];

    protected $casts = [
        'encryption_type' => 'string',
    ];
}
