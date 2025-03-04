<?php

namespace App\Services;

use App\Models\Ayam;
use App\Models\Panen;
use App\Models\AyamMati;
use App\Models\PakanMasuk;
use App\Models\PakanKeluar;
use App\Models\Populasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexPerformanceService 
{
    public function calculateIP(int $kandangId, string $periode) 
    {
        try {
            // Validate and get active Ayam record
            $ayam = Ayam::where('kandang_id', $kandangId)
                       ->where('periode', $periode)
                       ->where('status', 'active')
                       ->first();
        
            if (!$ayam) {
                throw new ModelNotFoundException('Data ayam tidak ditemukan untuk kandang dan periode yang dipilih');
            }
    
            // Get harvest data first
            $harvestData = $this->getDataPanen($ayam->id_ayam);
            if (!$harvestData['success']) {
                throw new \Exception($harvestData['message']);
            }
    
            // Calculate all components
            $dayaHidup = $this->hitungDayaHidup($ayam->id_ayam);
            $bobotBadan = $this->hitungBobotBadan($ayam->id_ayam);
            $umur = $this->hitungUmur($ayam);
            $fcr = $this->hitungFCR($ayam->id_ayam);
            
            // Calculate final IP
            $ip = $this->hitungIndexPerformance($dayaHidup, $bobotBadan, $umur, $fcr);
        
            // Save the results to Ayam model
            $ayam->update([
                'deplesi' => 100 - $dayaHidup,
                'fcr' => $fcr,
                'ip' => $ip,
                'umur' => $umur
            ]);
        
            return [
                'success' => true,
                'data' => [
                    'ip' => round($ip, 2),
                    'harvest_data' => $harvestData['data'],
                    'komponen' => [
                        'daya_hidup' => round($dayaHidup, 2) . '%',
                        'bobot_badan' => round($bobotBadan, 3) . ' kg',
                        'umur' => $umur . ' hari',
                        'fcr' => round($fcr, 3)
                    ],
                    'ayam_id' => $ayam->id_ayam // Pastikan ayam_id ada di sini
                ]
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Data ayam tidak ditemukan untuk kandang dan periode yang dipilih'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghitung IP: ' . $e->getMessage()
            ];
        }
    }


    private function hitungIndexPerformance(
        float $dayaHidup,
        float $bobotPanenRataRata,
        int $umurRataRata,
        float $fcr
    ): float {
        if ($umurRataRata === 0 || $fcr === 0) {
            throw new \Exception('Umur dan FCR tidak boleh 0');
        }
        
        // Rumus baru sesuai permintaan
        return ($dayaHidup * $bobotPanenRataRata * 100) / ($umurRataRata * $fcr);
    }


    public function getDataPanen(int $ayamId)
    {
        try {
            // Ambil data ayam
            $ayam = Ayam::findOrFail($ayamId);
            
            // Query untuk mengambil data panen beserta data harga dari tabel harga_ayam
            $dataPanen = DB::table('panen as p')
                ->join('monitoring_ayam as ma', function($join) {
                    $join->on('p.ayam_id', '=', 'ma.ayam_id')
                         ->whereRaw('DATE(p.tanggal_panen) = DATE(ma.tanggal)');
                })
                ->leftJoin('harga_ayam as ha', 'p.harga_id', '=', 'ha.id_harga')
                ->where('p.ayam_id', $ayamId)
                ->select([
                    'p.tanggal_panen',
                    'ma.age_day as umur',
                    DB::raw('ROUND(p.quantity / ' . $ayam->qty_ayam . ', 3) as persen_panen'),
                    'p.quantity as jumlah_panen',
                    'p.berat_total as total_bb_panen',
                    DB::raw('ma.age_day * p.quantity as age_quantity'),
                    'ha.harga as harga',       // kolom harga per ekor dari tabel harga_ayam
                    'p.total_panen'            // kolom total_panen dari tabel panen
                ])
                ->orderBy('p.tanggal_panen')
                ->get();
                
            // Hitung total-total
            $total = [
                'total_persen' => ($dataPanen->sum('jumlah_panen') / $ayam->qty_ayam) * 100,  // Menghitung persen dari jumlah_panen
                'total_jumlah' => $dataPanen->sum('jumlah_panen'),
                'total_bb' => $dataPanen->sum('total_bb_panen'),
                'total_age_quantity' => $dataPanen->sum('age_quantity'),
                'total_panen'        => $dataPanen->sum('total_panen'),      // Total Terjual
                'average_harga'      => $dataPanen->avg('harga')  
            ];
            Log::info('Data Panen:', $dataPanen->toArray());
    
            // Hitung total-total tambahan (jika diperlukan)
            $totalJumlahPanen = $dataPanen->sum('jumlah_panen');
            $totalAgeQuantity = $dataPanen->sum('age_quantity');
            $totalBBPanen = $dataPanen->sum('total_bb_panen');
            $totalPersen = $ayam->qty_ayam > 0 ? ($totalJumlahPanen / $ayam->qty_ayam) * 100 : 0;
            
            Log::info('Total Persen: ' . $totalPersen);
            Log::info('Total Age Quantity: ' . $totalAgeQuantity);
        
            // Hitung rata-rata umur
            $umurRataRata = $totalAgeQuantity / $totalJumlahPanen;
        
            // Ambil data pakan
            $dataPakan = DB::table('pakan_keluar as pk')
                ->join('pakan as p', 'pk.pakan_id', '=', 'p.id_pakan')
                ->where('pk.ayam_id', $ayamId)
                ->select('p.id_pakan', 'p.nama_pakan', 
                        DB::raw('SUM(pk.total_berat) as total_qty'),
                        DB::raw('SUM(pk.total_berat) as total_berat'))
                ->groupBy('p.id_pakan', 'p.nama_pakan')
                ->get();
        
            // Hitung total pakan terpakai
            $totalPakanTerpakai = $dataPakan->sum('total_berat');
        
            // Hitung bobot panen rata-rata
            $bobotPanenRataRata = $totalBBPanen / $totalJumlahPanen;
        
            // Hitung daya hidup
            $dayaHidup = ($totalJumlahPanen / $ayam->qty_ayam) * 100;
        
            // Hitung FCR
            $fcr = $totalPakanTerpakai / $totalBBPanen;
    
            // Hitung IP
            $ip = ($dayaHidup * $bobotPanenRataRata * 100) / ($umurRataRata * $fcr);
        
            return [
                'success' => true,
                'data' => [
                    'records' => $dataPanen,
                    'total' => $total,
                    'ringkasan' => [
                        'umur_rata_rata' => round($umurRataRata, 2),
                        'data_pakan' => $dataPakan,
                        'totalPersen' => $totalPersen,
                        'total_pakan_terpakai' => $totalPakanTerpakai,
                        'bobot_panen_rata_rata' => round($bobotPanenRataRata, 3),
                        'daya_hidup' => round($dayaHidup, 2),
                        'fcr' => round($fcr, 3),
                        'ip' => round($ip, 2)
                    ]
                ]
            ];
        
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data panen:', [
                'ayam_id' => $ayamId, 
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Gagal mengambil data panen: ' . $e->getMessage()
            ];
        }
    }
    
    //get data populasi
    public function getPopulasiData($populasi)
    {
        try {
            // Mengambil data ayam berdasarkan ID dari tabel ayam
            $ayam = Ayam::findOrFail($populasi); // Menggunakan ID dari tabel ayam yang sesuai
            
            // Mengambil total ayam mati dari tabel populasi berdasarkan foreign key 'populasi'
            $ayamMati = Populasi::where('populasi', $populasi) // Kolom 'populasi' sebagai FK
                                ->sum('qty_mati');
    
            // Mengambil total ayam sisa dari data terakhir
            $ayamSisa = Populasi::where('populasi', $populasi)
                                ->orderBy('tanggal', 'desc')
                                ->value('total');
    
            // Mengambil total ayam yang sudah dipanen
            $ayamPanen = Populasi::where('populasi', $populasi)
                                 ->sum('qty_panen');
    
            return [
                'status' => true,
                'data' => [
                    'populasi_awal' => $ayam->qty_ayam,
                    'ayam_mati' => $ayamMati,
                    'ayam_sisa' => $ayamSisa,
                    'ayam_panen' => $ayamPanen,
                ],
                'message' => 'Data populasi kandang berhasil diambil',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'data' => null,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }
    
    // Method tambahan untuk validasi data
    private function validateData($data)
    {
        // Memastikan semua nilai tidak null
        if (is_null($data['populasi_awal']) || 
            is_null($data['ayam_mati']) || 
            is_null($data['ayam_sisa']) || 
            is_null($data['ayam_panen'])) {
            return false;
        }
        
        return true;
    }

    private function hitungDayaHidup(int $ayamId): float
    {
        $ayam = Ayam::findOrFail($ayamId);
        $populasiAwal = $ayam->qty_ayam;
        
        $totalMati = AyamMati::where('ayam_id', $ayamId)
                            ->sum('quantity_mati');
        
        if ($populasiAwal === 0) {
            throw new \Exception('Populasi awal ayam tidak boleh 0');
        }
        
        $ayamHidup = $populasiAwal - $totalMati;
        return ($ayamHidup / $populasiAwal) * 100;
    }

    private function hitungBobotBadan(int $ayamId): float
    {
        $panen = Panen::where('ayam_id', $ayamId)
                     ->selectRaw('SUM(berat_total) as total_berat, SUM(quantity) as total_ayam')
                     ->first();
                     
        if (!$panen || $panen->total_ayam == 0) {
            throw new \Exception('Data panen tidak ditemukan atau jumlah ayam panen 0');
        }
        
        return $panen->total_berat / $panen->total_ayam;
    }

    private function hitungUmur(Ayam $ayam): int
    {
        $tanggalMulai = Carbon::parse($ayam->tanggal_masuk);
        $tanggalAkhir = $ayam->tanggal_selesai 
            ? Carbon::parse($ayam->tanggal_selesai)
            : Carbon::now();
        
        $umur = $tanggalMulai->diffInDays($tanggalAkhir);
        
        if ($umur <= 0) {
            throw new \Exception('Umur ayam tidak valid');
        }
        
        return $umur;
    }

    private function hitungFCR(int $ayamId): float
    {
        // Calculate total feed used
        $totalPakanMasuk = PakanMasuk::where('ayam_id', $ayamId)
                                    ->sum('total_berat');
        
        $totalPakanKeluar = PakanKeluar::where('ayam_id', $ayamId)
                                      ->sum('total_berat');
                                      
        $totalPakanTerpakai = $totalPakanMasuk - $totalPakanKeluar;
        
        // Get total chicken weight
        $totalBeratAyam = Panen::where('ayam_id', $ayamId)
                              ->sum('berat_total');
                              
        if ($totalBeratAyam === 0) {
            throw new \Exception('Total berat ayam panen tidak boleh 0');
        }
        
        if ($totalPakanTerpakai <= 0) {
            throw new \Exception('Total pakan terpakai tidak valid');
        }
        
        return $totalPakanTerpakai / $totalBeratAyam;
    }

    // private function hitungIndexPerformance(
    //     float $dayaHidup,
    //     float $bobotBadan,
    //     int $umur,
    //     float $fcr
    // ): float {
    //     if ($umur === 0 || $fcr === 0) {
    //         throw new \Exception('Umur dan FCR tidak boleh 0');
    //     }
        
    //     return ($dayaHidup * $bobotBadan * 100) / ($umur * $fcr);
    // }

    public function getEstimasiPembelian($ayamId)
{
    // 1. Ambil data ayam, termasuk kolom total_harga (DOC) dan doc_id
    $ayam = Ayam::with('doc') // opsional, jika Anda ingin ambil data harga_doc juga
               ->findOrFail($ayamId);

    // Nilai total_harga untuk DOC diambil langsung dari tabel ayam
    // misalnya 83.400.000
    $docTotalHarga = $ayam->total_harga; 
    // doc_id bisa diakses dengan $ayam->doc_id
    // jika Anda butuh hargaDoc->harga, misalnya $ayam->hargaDoc->harga

    // 2. Group pakan dari tabel pakan_masuk
    $pakanData = DB::table('pakan_masuk as pm')
        ->join('pakan as pk', 'pm.pakan_id', '=', 'pk.id_pakan')
        ->where('pm.ayam_id', $ayamId)
        ->select([
            'pm.pakan_id',
            'pk.nama_pakan',
            'pk.harga',
            DB::raw('SUM(pm.total_berat) as total_qty'),
            DB::raw('SUM(pm.total_harga_pakan) as total_harga')
        ])
        ->groupBy('pm.pakan_id', 'pk.nama_pakan')
        ->get();

    // Hitung total pakan
    $totalPakan = $pakanData->sum('total_harga');

    // 3. Ambil total obat dari tabel obat
    $totalObat = DB::table('obat')
        ->where('ayam_id', $ayamId)
        ->sum('total');  // misal 5.000.000

    // 4. Hitung total pembelian
    $totalPembelian = $docTotalHarga + $totalPakan + $totalObat;

    // Return data siap tampil di Blade
    return [
        'doc' => [
            'doc_id'      => $ayam->doc_id,       // jika Anda ingin menampilkan doc_id
            'total_harga' => $docTotalHarga,      // kolom total_harga dari tabel ayam
        ],
        'pakan' => $pakanData,
        'obat'  => $totalObat,
        'total_pembelian' => $totalPembelian
    ];
}

}
