<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaDoc extends Model
{
    use HasFactory;
    protected $table = 'harga_doc';
    protected $primaryKey = 'id_doc';
    protected $fillable = [
        'id_doc',
        'harga',
        'tahun',
    ];
}
