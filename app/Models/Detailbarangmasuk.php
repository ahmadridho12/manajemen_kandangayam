<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Detailbarangmasuk extends Model
{
    use HasFactory;
    protected $table = 'detail_masuk';

    protected $primaryKey = 'id_detailmasuk';

    protected $fillable = [
        'barang_masuk_id',
        'id_barang',
        'jumlah',
        'harga_sebelum_ppn',
        'kategori_ppn_id',
        'harga_setelah_ppn',
        'total_setelah_ppn',
        'tanggal_detailmasuk',
    ];

    public function barang()
    {
        return $this->belongsTo(Barangg::class, 'id_barang');
    }

    public function kategoribm()
    {
        return $this->belongsTo(Kategoribm::class, 'kategori_ppn_id');
    }

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
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
