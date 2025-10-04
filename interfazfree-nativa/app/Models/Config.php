<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $fillable = [
        'db_user',
        'db_password',
        'allowed_characters',
        'encryption_type',
    ];

    protected $casts = [
        'encryption_type' => 'string',
    ];
}
