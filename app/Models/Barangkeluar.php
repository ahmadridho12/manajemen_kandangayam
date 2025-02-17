<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Permintaan;
use App\Models\Detailbarangkeluar;
use App\Models\Barangg;
use Carbon\Carbon;


class Barangkeluar extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar';
    protected $primaryKey = 'id_keluar';

    // protected $primaryKey = 'nNotrans';

    protected $fillable = [
        'no_transaksi',
        'id_permintaan',
        'tanggal_keluar',
        
    ];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'id_permintaan', 'id_permintaan'); // Sesuaikan kolomnya
    }
    
    public function detailBarangKeluar()
{
    return $this->hasMany(Detailbarangkeluar::class, 'id_barangkeluar', 'id_keluar');
}

    public function barang()
    {
    return $this->belongsTo(Barangg::class, 'id_barang', 'id_barang'); // Sesuaikan dengan nama kolom foreign key
    }

    // Scope untuk mendapatkan barang keluar hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_keluar', Carbon::today());
    }

    // Scope untuk mendapatkan barang keluar kemarin
    public function scopeYesterday($query)
    {
        return $query->whereDate('tanggal_keluar', Carbon::yesterday());
    }
}

