<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radpostauth extends Model
{
    use HasFactory;

    protected $table = 'radpostauth';

    protected $fillable = [
        'username',
        'pass',
        'reply',
        'authdate',
    ];

    protected $casts = [
        'authdate' => 'datetime',
    ];
}
