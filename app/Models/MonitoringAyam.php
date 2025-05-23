<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\MonitoringGeneratorService;


class MonitoringAyam extends Model
{
    use HasFactory;

    protected $table = 'monitoring_ayam';
    protected $primaryKey = 'id';
    protected $fillable = [
        'ayam_id',
        'id_kandang',
        'tanggal',
        'age_day',
        'skat_1_bw',
        'skat_1_dg',
        'skat_2_bw',
        'skat_2_dg',
        'skat_3_bw',
        'skat_3_dg',
        'skat_4_bw',
        'skat_4_dg',
        'skat_5_bw',
        'skat_5_dg',
        'skat_6_bw',
        'skat_6_dg',
        'skat_7_bw',
        'skat_7_dg',
        'skat_8_bw',
        'skat_8_dg',
        'tanggal_monitoring',
        'body_weight',
        'daily_gain',
        
    ];

    protected $appends = [
        'skat_1_bw_status', 'skat_2_bw_status', 'skat_3_bw_status', 'skat_4_bw_status',
        'skat_1_dg_status', 'skat_2_dg_status', 'skat_3_dg_status', 'skat_4_dg_status'
    ];

    private function checkBwStatus($value, $ageDay)
    {
        $service = new \App\Services\MonitoringGeneratorService();
        $standardWeight = $service->getStandardWeight();
        $standard = $standardWeight[$ageDay] ?? null;
        
        if (!$standard || $value === null) return 'normal';
        return $value >= $standard['bw'] ? 'above' : 'below';
    }

    private function checkDgStatus($value, $ageDay)
    {
        $service = new \App\Services\MonitoringGeneratorService();
        $standardWeight = $service->getStandardWeight();
        $standard = $standardWeight[$ageDay] ?? null;
        
        if (!$standard || $value === null) return 'normal';
        return $value >= $standard['dg'] ? 'above' : 'below';
    }

    // Accessor untuk setiap skat body weight
    public function getSkat1BwStatusAttribute()
    {
        return $this->checkBwStatus($this->skat_1_bw, $this->age_day);
    }

    public function getSkat2BwStatusAttribute()
    {
        return $this->checkBwStatus($this->skat_2_bw, $this->age_day);
    }

    public function getSkat3BwStatusAttribute()
    {
        return $this->checkBwStatus($this->skat_3_bw, $this->age_day);
    }

    public function getSkat4BwStatusAttribute()
    {
        return $this->checkBwStatus($this->skat_4_bw, $this->age_day);
    }

    // Accessor untuk setiap skat daily gain
    public function getSkat1DgStatusAttribute()
    {
        return $this->checkDgStatus($this->skat_1_dg, $this->age_day);
    }

    public function getSkat2DgStatusAttribute()
    {
        return $this->checkDgStatus($this->skat_2_dg, $this->age_day);
    }

    public function getSkat3DgStatusAttribute()
    {
        return $this->checkDgStatus($this->skat_3_dg, $this->age_day);
    }

    public function getSkat4DgStatusAttribute()
    {
        return $this->checkDgStatus($this->skat_4_dg, $this->age_day);
    }
    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }
    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'id_kandang');
    }
}
