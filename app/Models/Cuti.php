<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cuti extends Model
{
    use HasFactory;
    protected $table = 'cuti'; // Tentukan nama tabel yang benar

    protected $primaryKey = 'id_cuti'; // Set the primary key

    protected $fillable = [
        'nama',
        'nik',
        'nama_jabatan',
        'golongan',
        'no_hp',
        'mulai_cuti',
        'sampai_cuti',
        'kantor',
        'tgl_buat',
        'nama_kuasa',
        'nik_kuasa',
        'jabatan_kuasa',
        'nohp_kuasa',
    ];

    public function unit()
    {
       return $this->belongsTo(Unit::class, 'unit_kerja', 'unit_kerja'); // Menggunakan 'id' sebagai primary key
    }
    public function jabatan()
    {
       return $this->belongsTo(jabatan::class, 'nama_jabatan', 'nama_jabatan'); // Menggunakan 'id' sebagai primary key
    }
}
