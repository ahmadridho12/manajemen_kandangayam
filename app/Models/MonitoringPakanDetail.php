<?php

namespace App\Models;
use App\Models\Ayam;
use App\Models\Pakan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPakanDetail extends Model
{
    use HasFactory;

    protected $table = 'monitoring_pakan_detail';
    protected $primaryKey = 'id_monitoring_pakan_detail';
    
    protected $fillable = [
        'pakan_id',
        'ayam_id',
        'masuk',
        'berat_zak',
        'total_berat'
    ];

    // public function monitoring()
    // {
    //     return $this->belongsTo(MonitoringPakan::class, 'monitoring_pakan_id', 'id_monitoring_pakan');
    // }
    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }
    public function pakan()
    {
        return $this->belongsTo(Pakan::class, 'pakan_id', 'id_pakan');
    }
}
