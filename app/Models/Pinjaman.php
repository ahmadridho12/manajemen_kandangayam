<?php

namespace App\Models;
use App\Models\Ayam;
use App\Models\Kandang;
use App\Models\Abk;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjaman_abk';
    protected $primaryKey = 'id_pinjaman';
    protected $fillable = [
        'abk_id',
        'kandang_id',
        'ayam_id',
        'jumlah_pinjaman',
        'tanggal_pinjam',
    ];

    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'id_kandang');
    }

    public function abk()
    {
        return $this->belongsTo(Abk::class, 'abk_id', 'id_abk');
    }
}
