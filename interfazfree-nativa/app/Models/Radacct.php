<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radacct extends Model
{
    use HasFactory;

    protected $table = 'radacct';
    protected $primaryKey = 'radacctid';

    protected $fillable = [
        'acctsessionid',
        'acctuniqueid',
        'username',
        'realm',
        'nasipaddress',
        'nasportid',
        'nasporttype',
        'acctstarttime',
        'acctupdatetime',
        'acctstoptime',
        'acctsessiontime',
        'acctauthentic',
        'connectinfo_start',
        'connectinfo_stop',
        'acctinputoctets',
        'acctoutputoctets',
        'calledstationid',
        'callingstationid',
        'acctterminatecause',
        'servicetype',
        'framedprotocol',
        'framedipaddress',
    ];

    protected $casts = [
        'acctstarttime' => 'datetime',
        'acctupdatetime' => 'datetime',
        'acctstoptime' => 'datetime',
        'acctsessiontime' => 'integer',
        'acctinputoctets' => 'integer',
        'acctoutputoctets' => 'integer',
    ];

    public function ficha()
    {
        return $this->belongsTo(Ficha::class, 'username', 'username');
    }
}
