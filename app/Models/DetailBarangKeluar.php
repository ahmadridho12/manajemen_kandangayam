<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DetailBarangKeluar extends Model // Ubah nama ini
{
    use HasFactory;

    protected $table = 'detail_barang_keluar';
    protected $primaryKey = 'id_detailkeluar';

    protected $fillable = [ // Perbaiki typo dari `filable` menjadi `fillable`
        'id_barangkeluar',
        'id_barang',
        'jumlah',
        'harga',
        'total',
        'tanggal_detailkeluar',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_barangkeluar', 'id_keluar');
    }
    public function barang()
    {
        return $this->belongsTo(Barangg::class, 'id_barang', 'id_barang');
    }
    public function stok()
    {
        return $this->belongsTo(Stok::class);
    }
    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    // Scope untuk mendapatkan barang keluar kemarin
    public function scopeYesterday($query)
    {
        return $query->whereDate('created_at', Carbon::yesterday());
    }
}
