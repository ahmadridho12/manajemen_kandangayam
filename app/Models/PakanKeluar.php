<?php

namespace App\Models;
use App\Models\Ayam;
use App\Models\Pakan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PakanKeluar extends Model
{
    use HasFactory;
    protected $table = 'pakan_keluar';
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'ayam_id',
        'pakan_id',
        'tanggal',
        'qty',
        'berat_zak',
        'total_berat',


    ];

    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }
    public function pakan()
    {
        return $this->belongsTo(Pakan::class, 'pakan_id', 'id_pakan');
    }
}
