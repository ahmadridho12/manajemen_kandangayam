<?php

namespace App\Models;

use App\Models\User;
use App\Models\Notrans;
use App\Models\Bagian;
use App\Models\DetailBarangKeluar;
use App\Models\Barangg;
use App\Models\Kategori;
use App\Models\Detailstok;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan';
    protected $primaryKey = 'id_permintaan';
    protected $fillable = [
        
        'id_user', 
        'no_trans',
        'tgl_permintaan',
        'keterangan',
        'bagian',
        'tipe_id',
        'status_persetujuan', // Kolom status persetujuan
        'tanggal_persetujuan', // Kolom tanggal persetujuan
        'id_user_persetujuan', // Kolom ID user yang menyetujui
    ];
    public function detailstok()
    {
        return $this->hasMany(Detailstok::class, 'id_detailstok');
    }
    public function detailPermintaan()
    {
        return $this->hasMany(DetailPermintaan::class, 'id_permintaan');
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'id_keluar');
    }

    public function detailkeluar()
    {
    return $this->hasMany(DetailBarangKeluar::class, 'id_detailkeluar');
    }
    

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    // public function notrans()
    // {
    //     return $this->belongsTo(Notrans::class, 'no_trans', 'id_notrans');
    // }
    public function userPersetujuan()
{
    return $this->belongsTo(User::class, 'id_user_persetujuan', 'id');
}
    public function bagiann()
    {
        return $this->belongsTo(Bagian::class, 'bagian', 'id_bagian');    }
    
        // Di dalam model DetailPermintaan
    public function barang()
    {
        return $this->belongsTo(Barangg::class, 'id_barang', 'id');
    }

    public function tipe()
    {
        return $this->belongsTo(Kategori::class, 'tipe_id', 'id_tipe'); // 'tipe_id' di tabel permintaan dan 'id_tipe' di tabel kategori
    }

     // Relasi dengan Tipe

     public function scopeToday($query)
    {
        return $query->whereDate('tgl_permintaan', Carbon::today());
    }

    // Scope untuk mendapatkan barang keluar kemarin
    public function scopeYesterday($query)
    {
        return $query->whereDate('tgl_permintaan', Carbon::yesterday());
    }
    
}
