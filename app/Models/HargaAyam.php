<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaAyam extends Model
{
    use HasFactory;
    protected $table = 'harga_ayam';
    protected $primaryKey = 'id_harga';
    protected $fillable = [
        'min_berat',
        'max_berat',
        'harga',
    ];
}
