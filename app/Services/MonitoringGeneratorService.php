<?php

namespace App\Services;

use App\Models\Ayam;
use App\Models\MonitoringAyam;
use Carbon\Carbon;

class MonitoringGeneratorService
{
    // Standar berat dan daily gain ayam per hari
    private $standardWeight = [
        0 => ['bw' => 0, 'dg' => 0],
        1 => ['bw' => 63, 'dg' => 14],
        2 => ['bw' => 74, 'dg' => 11],
        3 => ['bw' => 90, 'dg' => 16],
        4 => ['bw' => 109, 'dg' => 19],
        5 => ['bw' => 134, 'dg' => 25],
        6 => ['bw' => 163, 'dg' => 29],
        7 => ['bw' => 193, 'dg' => 30],
        8 => ['bw' => 228, 'dg' => 35],
        9 => ['bw' => 269, 'dg' => 41],
        10 => ['bw' => 313, 'dg' => 44],
        11 => ['bw' => 362, 'dg' => 49],
        12 => ['bw' => 414, 'dg' => 52],
        13 => ['bw' => 469, 'dg' => 55],
        14 => ['bw' => 528, 'dg' => 59],
        15 => ['bw' => 589, 'dg' => 61],
        16 => ['bw' => 654, 'dg' => 65],
        17 => ['bw' => 722, 'dg' => 68],
        18 => ['bw' => 792, 'dg' => 70],
        19 => ['bw' => 865, 'dg' => 73],
        20 => ['bw' => 941, 'dg' => 76],
        21 => ['bw' => 1018, 'dg' => 77],
        22 => ['bw' => 1098, 'dg' => 80],
        23 => ['bw' => 1180, 'dg' => 82],
        24 => ['bw' => 1264, 'dg' => 84],
        25 => ['bw' => 1349, 'dg' => 85],
        26 => ['bw' => 1436, 'dg' => 87],
        27 => ['bw' => 1525, 'dg' => 89],
        28 => ['bw' => 1615, 'dg' => 90],
        29 => ['bw' => 1706, 'dg' => 91],
        30 => ['bw' => 1798, 'dg' => 92],
        31 => ['bw' => 1892, 'dg' => 94],
        32 => ['bw' => 1986, 'dg' => 94],
        33 => ['bw' => 2081, 'dg' => 95],
        34 => ['bw' => 2177, 'dg' => 96],
        35 => ['bw' => 2273, 'dg' => 96],
        36 => ['bw' => 2369, 'dg' => 96],
        37 => ['bw' => 2466, 'dg' => 97],
        38 => ['bw' => 2563, 'dg' => 97],
        39 => ['bw' => 2661, 'dg' => 98],
        40 => ['bw' => 2758, 'dg' => 97],
        41 => ['bw' => 2855, 'dg' => 97],
        42 => ['bw' => 2952, 'dg' => 97],
        // ... tambahkan data standar sampai hari ke-36
    ];
    public function getStandardWeight()
    {
        return $this->standardWeight;
    }

    public function generateFromAyam($ayamId)
    {
        $ayam = Ayam::findOrFail($ayamId);
        $startDate = Carbon::parse($ayam->tanggal_masuk);
        $rentangHari = $ayam->rentang_hari; // Ambil rentang hari dari data ayam
    
        // Generate data sesuai rentang hari yang diinput
        for ($day = 0; $day <= $rentangHari; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
    
            // Ambil standar berat dan pertumbuhan untuk hari ini
            $standard = $this->standardWeight[$day] ?? [
                'bw' => $this->estimateWeight($day),
                'dg' => $this->estimateDailyGain($day)
            ];
    
            // Buat record monitoring untuk hari ini
            $monitoring = new MonitoringAyam();
    
            $monitoring->ayam_id = $ayam->id_ayam;
            $monitoring->age_day = $day;
            $monitoring->tanggal = $currentDate;
    
            // Hanya untuk hari ke-0
            if ($day === 0) {
                $monitoring->kandang_id = $ayam->kandang_id ?? null;
                $monitoring->body_weight = 0;  // Nilai default untuk hari pertama
                $monitoring->daily_gain = 0;   // Nilai default untuk hari pertama
            } else {
                $monitoring->kandang_id = $ayam->kandang_id ?? null;
                $monitoring->body_weight = $standard['bw']; 
                $monitoring->daily_gain = $standard['dg'];
            }
    
            $monitoring->save();
        }
    }

    private function estimateWeight($day)
    {
        // Estimasi berat jika tidak ada di standar
        // Menggunakan rumus pertumbuhan eksponensial sederhana
        $baseWeight = 63; // Berat awal
        $growthRate = 0.15; // Rate pertumbuhan
        return round($baseWeight * pow(1 + $growthRate, $day), 2);
    }

    private function estimateDailyGain($day)
    {
        // Estimasi daily gain jika tidak ada di standar
        if ($day <= 0) return 0;
        
        $currentWeight = $this->estimateWeight($day);
        $previousWeight = $this->estimateWeight($day - 1);
        return round($currentWeight - $previousWeight, 2);
    }

    public function updateMeasurement($monitoringId, array $data)
{
    $monitoring = MonitoringAyam::findOrFail($monitoringId);
    
    // Periksa apakah hari ini memiliki standar berat badan
    $standardWeight = $this->standardWeight[$monitoring->age_day] ?? null;
    
    $updateData = [];
    
    // Jika ada standar berat badan, gunakan standar tersebut untuk body_weight dan daily_gain
    if ($standardWeight) {
        $updateData['body_weight'] = $standardWeight['bw'];
        $updateData['daily_gain'] = $standardWeight['dg'];
    }

    // Dapatkan data monitoring sehari sebelumnya
    $previousMonitoring = MonitoringAyam::where('ayam_id', $monitoring->ayam_id)
        ->where('age_day', $monitoring->age_day - 1)
        ->first();

    $totalBw = 0;
    $countBw = 0;

    // Update data setiap sekat
    foreach (['1', '2', '3', '4'] as $skat) {
        $bwKey = "skat_{$skat}_bw";
        
        if (isset($data[$bwKey])) {
            $updateData[$bwKey] = $data[$bwKey];
            
            // Hitung daily gain untuk sekat ini
            if ($previousMonitoring && $previousMonitoring->{$bwKey} > 0) {
                $updateData["skat_{$skat}_dg"] = 
                    round($data[$bwKey] - $previousMonitoring->{$bwKey}, 2);
            }

            // Tambahkan ke total untuk rata-rata
            if ($data[$bwKey] > 0) {
                $totalBw += $data[$bwKey];
                $countBw++;
            }
        }
    }

    // Hitung rata-rata body weight dan daily gain untuk hari-hari di luar standar
    if (!$standardWeight && $countBw > 0) {
        $updateData['body_weight'] = round($totalBw / $countBw, 2);
        
        if ($previousMonitoring) {
            $updateData['daily_gain'] = 
                round($updateData['body_weight'] - $previousMonitoring->body_weight, 2);
        }
    }

    $monitoring->update($updateData);
    return $monitoring;
}
}
