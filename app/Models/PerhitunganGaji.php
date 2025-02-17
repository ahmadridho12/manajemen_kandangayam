<?php

namespace App\Models;
use App\Models\Kandang;
use App\Models\Ayam;
use App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerhitunganGaji extends Model
{
    use HasFactory;
    protected $table = 'perhitungan_gaji';
    protected $primaryKey = 'id_perhitungan';
    
    protected $fillable = [
        'ayam_id',
        'kandang_id',
        'hasil_pemeliharaan',
        'total_potongan',
        'hasil_setelah_potongan',
        'bonus_per_orang',
        'keterangan'
    ];

    public function rincianGaji()
    {
        // Sesuaikan dengan nama kolom di tabel rincian_gaji_abk
        return $this->hasMany(RincianGajiAbk::class, 'perhitungan_id', 'id_perhitungan');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'id_kandang');
    }

    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }
    public function operasional()
{
    return $this->hasMany(Operasional::class, 'ayam_id', 'ayam_id')
                ->where('kandang_id', $this->kandang_id);
}

}
