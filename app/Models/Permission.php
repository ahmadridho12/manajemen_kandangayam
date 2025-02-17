<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_permission'; // Set the primary key

    protected $fillable = [
        'nama',
        'nik',
        'golongan',
        'unit_kerja',
        'nama_jabatan',
        'tgl_buat',
        'tgl_mulai',
        'tgl_selesai',
        'perihal',
        'foto',
    ];
     // Definisikan relasi dengan model Unit
     public function unit()
     {
        return $this->belongsTo(Unit::class, 'unit_kerja', 'unit_kerja'); // Menggunakan 'id' sebagai primary key
     }
     public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'nama_jabatan', 'nama_jabatan');
    }
}
