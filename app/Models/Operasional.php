<?php

namespace App\Models;
use App\Models\Ayam;
use App\Models\Kandang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operasional extends Model
{
    use HasFactory;
    protected $table = 'potongan_operasional';
    protected $primaryKey = 'id_potongan';
    protected $fillable = [
        'kandang_id',
        'ayam_id',
        'nama_potongan',
        'jumlah',
        'tanggal'
    ];

    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'id_kandang');
    }

}
