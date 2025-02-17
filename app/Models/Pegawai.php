<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai'; // Tambahkan ini jika 'id_pegawai' adalah primary key

    protected $fillable = [
        'nama_pegawai',
        'nik',
        // Tambahkan kolom lain yang diperlukan
    ];

    // Relasi ke Jabatan jika diperlukan
    public function jabatan()
    {
        return $this->hasOne(Jabatan::class, 'id_pegawai');
    }
    
}
