<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barangg;  // Import model Barangg
use App\Models\Jenis;
use App\Models\Detailstok;  // Import model Jenis

class Stok extends Model
{
    use HasFactory;
    protected $table = 'stok';
    protected $primaryKey = 'id_stok';

     // Kolom yang bisa diisi
     protected $fillable = [
        'id_barang', 
        'id_jenis', 
        'harga', 
        'qty',
        'total',
    ];

    // Relasi ke tabel `barangg`
    public function barangg()
    {
        return $this->belongsTo(Barangg::class, 'id_barang', 'id_barang');
    }

    // Relasi ke tabel `jenis`
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis', 'id');
    }

    // Relasi ke tabel `permintaan`
    public function permintaan()
    {
        return $this->hasMany(Permintaan::class, 'id_permintaan', 'id_permintaan');
    }

    public function detailMasuk()
{
    return $this->hasMany(Detailbarangmasuk::class, 'id_barang', 'id_barang')
        ->join('barang_masuk', 'detail_masuk.barang_masuk_id', '=', 'barang_masuk.id_masuk')
        ->orderBy('barang_masuk.tgl_masuk', 'asc');
}
    // Stok Model
    public function detailStok()
    {
        return $this->hasMany(Detailstok::class, 'stok_id', 'id_stok');
    }

    // public function scopeToday($query)
    // {
    //     return $query->whereDate('created_at', Carbon::today());
    // }

    // // Scope untuk mendapatkan barang keluar kemarin
    // public function scopeYesterday($query)
    // {
    //     return $query->whereDate('created_at', Carbon::yesterday());
    // }
}
