<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ficha extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'estado',
        'fecha_inicio',
        'fecha_expiracion',
        'perfil_id',
        'lote_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_expiracion' => 'datetime',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function radcheck()
    {
        return $this->hasMany(Radcheck::class, 'username', 'username');
    }

    public function radreply()
    {
        return $this->hasMany(Radreply::class, 'username', 'username');
    }

    public function radacct()
    {
        return $this->hasMany(Radacct::class, 'username', 'username');
    }

    public function actualizarEstado()
    {
        if ($this->estado === 'sin_usar') {
            $sesiones = $this->radacct()->count();
            if ($sesiones > 0) {
                $this->update([
                    'estado' => 'activa',
                    'fecha_inicio' => $this->radacct()->min('acctstarttime')
                ]);
            }
        }

        if ($this->fecha_expiracion && Carbon::now()->isAfter($this->fecha_expiracion)) {
            $this->update(['estado' => 'caducada']);
        }

        return $this->estado;
    }
    
    public function getTiempoUsadoAttribute()
    {
        return $this->radacct()->sum('acctsessiontime') ?? 0;
    }
}
