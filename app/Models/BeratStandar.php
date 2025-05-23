<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeratStandar extends Model
{
    use HasFactory;
    protected $table = 'berat_standar';
    protected $primaryKey = 'id';
    protected $fillable = [
        'hari_ke',
        'bw',
        'dg',
    ];
}
