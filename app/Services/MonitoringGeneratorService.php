<?php

namespace App\Services;

use App\Models\Ayam;
use App\Models\MonitoringAyam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
    public static function getStandard($day)
{
    $standards = [
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
    ];

    return $standards[$day] ?? ['bw' => 0, 'dg' => 0];
}


    public function generateFromAyam($ayamId)
{
    $ayam = Ayam::findOrFail($ayamId);
    $startDate = Carbon::parse($ayam->tanggal_masuk);
    $rentangHariBaru = $ayam->rentang_hari;

    // Ambil hari terakhir yang sudah ada di monitoring
    $monitoringTerakhir = MonitoringAyam::where('ayam_id', $ayam->id_ayam)
        ->orderByDesc('age_day')
        ->first();

    $hariTerakhirTersimpan = $monitoringTerakhir ? $monitoringTerakhir->age_day : -1;

    // Hapus jika ada data melebihi rentang baru (kalau rentang dikurangi)
    MonitoringAyam::where('ayam_id', $ayam->id_ayam)
        ->where('age_day', '>', $rentangHariBaru)
        ->delete();

    // Tambahkan data baru jika rentang bertambah
    for ($day = $hariTerakhirTersimpan + 1; $day <= $rentangHariBaru; $day++) {
        $currentDate = $startDate->copy()->addDays($day);

        $standard = $this->standardWeight[$day] ?? [
            'bw' => $this->estimateWeight($day),
            'dg' => $this->estimateDailyGain($day)
        ];

        $monitoring = new MonitoringAyam();
        $monitoring->ayam_id = $ayam->id_ayam;
        $monitoring->age_day = $day;
        $monitoring->tanggal = $currentDate;
        $monitoring->kandang_id = $ayam->kandang_id ?? null;

        if ($day === 0) {
            $monitoring->body_weight = 0;
            $monitoring->daily_gain = 0;
        } else {
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
    private function recalculateSubsequentDailyGains($ayamId, $fromAgeDay)
{
    // Ambil semua monitoring setelah hari yang diupdate, urutkan berdasarkan age_day
    $subsequentMonitorings = MonitoringAyam::where('ayam_id', $ayamId)
        ->where('age_day', '>', $fromAgeDay)
        ->orderBy('age_day', 'asc')
        ->get();

    foreach ($subsequentMonitorings as $monitoring) {
        // Cari monitoring sebelumnya (hari sebelumnya)
        $previousMonitoring = MonitoringAyam::where('ayam_id', $ayamId)
            ->where('age_day', $monitoring->age_day - 1)
            ->first();

        if ($previousMonitoring) {
            // Hitung ulang daily gain
            $newDailyGain = round($monitoring->body_weight - $previousMonitoring->body_weight, 2);
            
            // Update daily gain
            $monitoring->update(['daily_gain' => $newDailyGain]);
        }
    }

    return $subsequentMonitorings;
}
  // di App/Services/MonitoringGeneratorService.php

/**
 * Simpan monitoring manual (dipanggil dari Controller::store)
 */
public function storeManualMonitoring(array $validated): MonitoringAyam
{
    $ayam = Ayam::with('kandang')->findOrFail($validated['ayam_id']);
    $jumlahSkat = $ayam->kandang->jumlah_skat ?? 4;

    // Hitung usia ayam (age_day)
    $tanggalMonitoring = Carbon::parse($validated['tanggal_monitoring']);
    $tanggalMasuk     = Carbon::parse($ayam->tanggal_masuk);
    $ageDay           = $tanggalMonitoring->diffInDays($tanggalMasuk);

    // Validasi tanggal
    if ($tanggalMonitoring->lt($tanggalMasuk)) {
        throw new \Exception("Tanggal monitoring tidak boleh sebelum tanggal masuk ayam ({$ayam->tanggal_masuk})");
    }

    // Cek duplikasi
    $existing = MonitoringAyam::where('ayam_id', $validated['ayam_id'])
        ->where('age_day', $ageDay)
        ->first();
    if ($existing) {
        // Jika sudah ada, update saja
        return $this->updateManualMonitoring($existing->id, $validated);
    }

    // Ambil standar untuk age_day
    $standard = $this->standardWeight[$ageDay] ?? null;
    if (! $standard) {
        throw new \Exception("Standar berat untuk hari ke-{$ageDay} belum tersedia.");
    }

    // Siapkan data untuk create()
    $data = [
        'ayam_id'      => $validated['ayam_id'],
        'tanggal'      => $validated['tanggal_monitoring'],
        'age_day'      => $ageDay,
        'body_weight'  => $standard['bw'],
        'daily_gain'   => $standard['dg'],
        'kandang_id'   => $ayam->kandang_id,
    ];

    // Masukkan tiap sampel skat
    for ($i = 1; $i <= $jumlahSkat; $i++) {
        $keyBw = "skat_{$i}_bw";
        if (! isset($validated[$keyBw])) {
            throw new \Exception("Data sampel ayam skat {$i} tidak ditemukan");
        }
        $data[$keyBw] = (float) $validated[$keyBw];

        // Hitung selisih terhadap hari sebelumnya untuk kolom skat_i_dg
        $prev = MonitoringAyam::where('ayam_id', $validated['ayam_id'])
                  ->where('age_day', $ageDay - 1)
                  ->first();
        if ($prev) {
            $data["skat_{$i}_dg"] = round($data[$keyBw] - ($prev->{$keyBw} ?? 0), 2);
        }
    }

    return MonitoringAyam::create($data);
}

/**
 * Update monitoring manual (dipanggil dari Controller::update atau store saat duplikat)
 */
public function updateManualMonitoring($monitoringId, array $validated): MonitoringAyam
{
    $monitoring = MonitoringAyam::with('ayam.kandang')->findOrFail($monitoringId);
    $jumlahSkat = $monitoring->ayam->kandang->jumlah_skat ?? 4;

    // Hitung age_day berdasar tanggal yang dikirim (atau gunakan tanggal existing)
    $tanggalMonitoring = isset($validated['tanggal_monitoring'])
        ? Carbon::parse($validated['tanggal_monitoring'])
        : Carbon::parse($monitoring->tanggal);
    $ageDay = $tanggalMonitoring->diffInDays(Carbon::parse($monitoring->ayam->tanggal_masuk));

    // Ambil standar
    $standard = $this->standardWeight[$ageDay] ?? null;
    if (! $standard) {
        throw new \Exception("Standar berat untuk hari ke-{$ageDay} belum tersedia.");
    }

    // Siapkan data update: body + daily gain dari standar
    $updateData = [
        'body_weight' => $standard['bw'],
        'daily_gain'  => $standard['dg'],
    ];

    // Proses tiap skat
    for ($i = 1; $i <= $jumlahSkat; $i++) {
        $keyBw = "skat_{$i}_bw";
        $keyDg = "skat_{$i}_dg";

        if (! isset($validated[$keyBw])) {
            throw new \Exception("Data sampel ayam skat {$i} tidak ditemukan");
        }
        $bw = (float) $validated[$keyBw];
        $updateData[$keyBw] = $bw;

        // Delta per skat terhadap hari sebelumnya
        $prev = MonitoringAyam::where('ayam_id', $monitoring->ayam_id)
                  ->where('age_day', $ageDay - 1)
                  ->first();
        if ($prev) {
            $updateData[$keyDg] = round($bw - ($prev->{$keyBw} ?? 0), 2);
        }
    }

    $monitoring->update($updateData);
    return $monitoring;
}


    public function updateMeasurement($monitoringId, array $data)
{
    $monitoring = MonitoringAyam::findOrFail($monitoringId);

    // Ambil jumlah skat dari kandang ayam
    $jumlahSkat = $monitoring->ayam->kandang->jumlah_skat ?? 4;

    // Periksa apakah hari ini memiliki standar berat badan
    $standardWeight = $this->standardWeight[$monitoring->age_day] ?? null;

    $updateData = [];

    // Jika ada standar berat badan, gunakan standar tersebut
    if ($standardWeight) {
        $updateData['body_weight'] = $standardWeight['bw'];
        $updateData['daily_gain'] = $standardWeight['dg'];
    }

    // Ambil data hari sebelumnya
    $previousMonitoring = MonitoringAyam::where('ayam_id', $monitoring->ayam_id)
        ->where('age_day', $monitoring->age_day - 1)
        ->first();

    $totalBw = 0;
    $countBw = 0;

    for ($skat = 1; $skat <= $jumlahSkat; $skat++) {
        $bwKey = "skat_{$skat}_bw";

        if (isset($data[$bwKey])) {
            $updateData[$bwKey] = $data[$bwKey];

            if ($previousMonitoring && $previousMonitoring->{$bwKey} > 0) {
                $updateData["skat_{$skat}_dg"] = 
                    round($data[$bwKey] - $previousMonitoring->{$bwKey}, 2);
            }

            if ($data[$bwKey] > 0) {
                $totalBw += $data[$bwKey];
                $countBw++;
            }
        }
    }

    // Kalau tidak ada standar dan ada data, hitung manual
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
