<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPakan extends Model
{
    use HasFactory;

    protected $table = 'monitoring_pakan';
    protected $primaryKey = 'id_monitoring_pakan';
    
    protected $fillable = [
        'ayam_id',
        'tanggal',
        'day',
        'total_masuk',
        'total_berat',
        'keluar',
        'sisa',
        'transfer_id',      // Kolom baru
        'total_transfer'    // Kolom baru
    ];
    
    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }

    public function details()
    {
        return $this->hasMany(MonitoringPakanDetail::class, 'monitoring_pakan_id', 'id_monitoring_pakan');
    }

    public function transfersFrom()
    {
        return $this->hasMany(PakanTransfer::class, 'monitoring_pakan_from_id', 'id_monitoring_pakan');
    }

    public function transfersTo()
    {
        return $this->hasMany(PakanTransfer::class, 'monitoring_pakan_to_id', 'id_monitoring_pakan');
    }
    
    // Relasi ke transfer jika diperlukan (satu record monitoring berkaitan dengan satu transfer)
    public function transfer()
    {
        return $this->belongsTo(PakanTransfer::class, 'transfer_id', 'id');
    }

}
