<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Permintaan;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'tipe';
    protected $primaryKey = 'id_tipe'; // Ganti dengan nama kolom kunci utama yang benar

    protected $fillable = [
        'id_tipe',
        'nama_tipe',
        'teruntuk',
    ];

    public function permintaan()
    {
        return $this->hasMany(Permintaan::class, 'tipe_id');
    }
}
