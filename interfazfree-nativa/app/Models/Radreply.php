<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radreply extends Model
{
    use HasFactory;

    protected $table = 'radreply';

    protected $fillable = [
        'username',
        'attribute',
        'op',
        'value',
    ];

    public function ficha()
    {
        return $this->belongsTo(Ficha::class, 'username', 'username');
    }
}
