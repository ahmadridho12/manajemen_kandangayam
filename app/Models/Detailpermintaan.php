<?php

namespace App\Models;

use App\Models\Barangg;
use App\Models\Detailbarangmasuk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailpermintaan extends Model
{
    use HasFactory;

    protected $table = 'detailpermintaan';

    protected $primaryKey = 'id_detailpermintaan';

    protected $fillable = [
        'id_permintaan',
        'id_barang',
        'qty',
        'harga',
        'tanggal_detailpermintaan',

    ];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'id_permintaan');
    }

    public function barang()
    {
        return $this->belongsTo(Barangg::class, 'id_barang');
    }
     // Tambahkan relasi ke DetailMasuk
     public function detailMasuk()
     {
         return $this->belongsTo(Detailbarangmasuk::class, 'id_barang', 'id_barang'); // Ubah 'id_barang' sesuai kolom yang tepat
     }
}
