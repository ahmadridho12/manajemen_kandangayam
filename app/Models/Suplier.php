<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suplier extends Model
{
    use HasFactory;

    protected $table = 'suplier';
    protected $primaryKey = 'kode';

    protected $fillable = [
        'kode',
        'namacp',
        'namaperus',
        'jabatan',
        'alamat',
        'npwp',
        'rating',
        'notes',
    ];
}
