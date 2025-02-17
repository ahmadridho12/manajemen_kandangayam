<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan'; // Nama tabel di database
    protected $primaryKey = 'id_jabatan'; // Primary key tabel

    protected $fillable = [
        'nama_jabatan', // Isi kolom yang dapat diisi
        'id_pegawai',
    ];

    // Relasi ke Permission
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'nama_jabatan', 'nama_jabatan'); 
    }

    // Relasi ke Pegawai
    public function pegawai()
{
    return $this->belongsTo(Pegawai::class, 'id_pegawai');
}
}
