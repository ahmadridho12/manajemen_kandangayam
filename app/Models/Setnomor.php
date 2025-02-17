<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setnomor extends Model
{
    use HasFactory;

    protected $table = 'setnomor';

    protected $primaryKey = 'majenis';
    protected $fillable = [
        'majenis',
        'txttengah',
        'lenlpad',
        'flagbulan',
        'pemisah',
        'flagauto',
        'pemisah2',
        'flagwilayah',
        'flagurutwil',
        'flagsemi',


    ];


}
