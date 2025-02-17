<?php

namespace App\Models;
use App\Models\Kandang;
use App\Models\Ayam;
use App\Models\Pakan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PakanTransfer extends Model
{
    use HasFactory;

    protected $table = 'pakan_transferS';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'tanggal',
        'kandang_asal_id',
        'kandang_tujuan_id',
        'ayam_asal_id',
        'ayam_tujuan_id',
        'pakan_id',
        'qty',
        'berat_zak',
        'total_berat',
        'keterangan'
    ];

    public function kandangAsal()
    {
        return $this->belongsTo(Kandang::class, 'kandang_asal_id', 'id_kandang');
    }

    public function kandangTujuan()
    {
        return $this->belongsTo(Kandang::class, 'kandang_tujuan_id', 'id_kandang');
    }

    public function ayamAsal()
    {
        return $this->belongsTo(Ayam::class, 'ayam_asal_id', 'id_ayam');
    }

    public function ayamTujuan()
    {
        return $this->belongsTo(Ayam::class, 'ayam_tujuan_id', 'id_ayam');
    }

    public function pakan()
{
    return $this->belongsTo(Pakan::class, 'pakan_id', 'id_pakan');
}
}
