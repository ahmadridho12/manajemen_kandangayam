<?php

namespace App\Models;
use App\Models\Kandang;
use App\Models\Ayam;
use App\Models\Pinjaman;
use App\Models\PerhitunganGaji;
use App\Models\Abk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianGajiAbk extends Model
{
    use HasFactory;

    protected $table = 'rincian_gaji_abk';
    protected $primaryKey = 'id_rincian';
    
    protected $fillable = [
        'perhitungan_id',
        'abk_id',
        'ayam_id',
        'kandang_id',
        'gaji_pokok',
        'bonus',
        'pinjaman_id',
        'gaji_bersih'
    ];

    public function abk()
{
    // Sesuaikan dengan nama kolom foreign key di tabel rincian_gaji_abk
    // dan primary key di tabel abk
    return $this->belongsTo(Abk::class, 'abk_id', 'id_abk');
}

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id', 'id_pinjaman');
    }
 
    public function perhitunganGaji()
    {
        return $this->belongsTo(PerhitunganGaji::class, 'perhitungan_id', 'id_perhitungan');
    }

}
