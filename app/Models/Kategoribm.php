<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategoribm extends Model
{
    use HasFactory;

    protected $table = 'kategoribm';
    protected $primaryKey = 'id_kategoribm';

    protected $fillable = [
        'id_kategoribm',
        'nama_ibm',
        'ppn',
    ];
}
