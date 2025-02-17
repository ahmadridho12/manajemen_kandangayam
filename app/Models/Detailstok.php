<?php

namespace App\Models;

use App\Models\Stok;
use App\Models\Detailbarangmasuk;
use App\Models\Barangg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailstok extends Model
{
    use HasFactory;


    protected $table = 'detail_stok';
    protected $primaryKey = 'id_detailstok';
    protected $fillable = [
        'stok_id',
        'detailmasuk_id',
        'barang_id',
        'qty_stok',
        'harga',
        'total'         
    ];

      // Relasi ke tabel Stok
      public function stok()
      {
          return $this->belongsTo(Stok::class, 'stok_id');
      }
  
      // Relasi ke tabel Detail Masuk
      public function detailMasuk()
      {
          return $this->belongsTo(Detailbarangmasuk::class, 'detailmasuk_id');
      }
  
      // Relasi ke tabel Barang
      public function barangg()
      {
          return $this->belongsTo(Barangg::class, 'barang_id');
      }
  
      // Method untuk mendapatkan harga setelah PPN dari detail_masuk
      public function getHargaSetelahPpnAttribute()
      {
          return $this->detailMasuk->harga_setelah_ppn;
      }
  
      // Method untuk mendapatkan total setelah PPN dari detail_masuk
      public function getTotalSetelahPpnAttribute()
      {
          return $this->detailMasuk->total_setelah_ppn;
      }
}
