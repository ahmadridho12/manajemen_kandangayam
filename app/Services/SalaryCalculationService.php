<?php

namespace App\Services;

use App\Models\PerhitunganGaji;
use App\Models\RincianGajiAbk;
use App\Models\PotonganOperasional;
use App\Models\PinjamanAbk;
use App\Models\Abk;
use App\Models\Operasional;
use App\Models\Pinjaman;
use App\Models\Ayam;
use App\Models\Kandang;
use App\Services\IndexPerformanceService;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryCalculationService
{

    protected $indexPerformanceService;

    public function __construct(IndexPerformanceService $indexPerformanceService)
    {
        $this->indexPerformanceService = $indexPerformanceService;
    }

    // Metode calculateLaba: menghitung laba bersih berdasarkan data performa
    public function calculateLaba($ayam_id)
    {
        // Ambil data panen dan estimasi pembelian menggunakan IndexPerformanceService
        $dataPanen = $this->indexPerformanceService->getDataPanen($ayam_id);
        $estimasiPembelian = $this->indexPerformanceService->getEstimasiPembelian($ayam_id);

        // Pastikan struktur data sudah sesuai
        $totalBB        = $dataPanen['data']['total']['total_bb']        ?? 0; // Contoh: total berat panen (kg)
        $avgHarga       = $dataPanen['data']['total']['average_harga']   ?? 0; // Contoh: rata-rata harga per ekor
        $totalPanen     = $dataPanen['data']['total']['total_panen']     ?? 0; // Contoh: total penjualan (rupiah)
        $totalPembelian = $estimasiPembelian['total_pembelian']          ?? 0; // Contoh: total pembelian (rupiah)

        // Hitung bonus, misalnya:
        $bonusFcr = $totalBB * 250;    // Contoh: 5.904.335
        $bonusKematian = $totalBB * 100;    // Contoh: 2.361.734

        // Total penjualan dianggap sama dengan totalPanen (penjualan daging)
        $totalPenjualan = $totalBB * $avgHarga; // Karena total_panen adalah hasil penjualan daging

        // Laba bersih: total penjualan + bonus - total pembelian
        $labaBersih = $totalPenjualan + $bonusFcr + $bonusKematian - $totalPembelian;

        return ['total_laba' => $labaBersih];
    }
    public function calculateSalary($ayam_id, $kandang_id, $hasil_pemeliharaan, $bonus_total, $keterangan = 0)
    {
        try {
            DB::beginTransaction();
    
            // 1. Ambil rincian potongan operasional khusus kandang ini
            $rincian_potongan = Operasional::where('ayam_id', $ayam_id)
                ->where('kandang_id', $kandang_id)
                ->select('nama_potongan', 'jumlah')
                ->get();
            $total_potongan = $rincian_potongan->sum('jumlah');
    
            // 2. Hitung hasil setelah potongan
            $hasil_setelah_potongan = $hasil_pemeliharaan - $total_potongan;
    
            // 3. Ambil jumlah ABK berdasarkan kandang
            $abk_aktif = Abk::where('status', 'active')
                ->where('kandang_id', $kandang_id)
                ->get();
            $jumlah_abk = $abk_aktif->count();
    
            if ($jumlah_abk == 0) {
                throw new \Exception("Tidak ada ABK aktif di kandang ini.");
            }
    
            // 4. Hitung gaji pokok per orang (20% dari hasil setelah potongan)
            $total_gaji_pokok = $hasil_setelah_potongan * 0.20;
            $gaji_pokok_per_orang = $total_gaji_pokok / $jumlah_abk;
    
            // 5. Hitung bonus per orang (jika ada)
            $bonus_per_orang = $bonus_total > 0 ? ($bonus_total / $jumlah_abk) : 0;
            
            // 6. Hitung total bonus
            $total_bonus = $bonus_per_orang * $jumlah_abk;
    
            // 7. Hitung total pengeluaran dan keuntungan perusahaan (laba bersih)
            $total_pengeluaran = $total_potongan + $total_gaji_pokok + $total_bonus;
            $keuntungan_perusahaan = $hasil_pemeliharaan - $total_pengeluaran;
    
            // 8. Buat record perhitungan gaji
            $perhitunganGaji = PerhitunganGaji::create([
                'ayam_id' => $ayam_id,
                'kandang_id' => $kandang_id,
                'hasil_pemeliharaan' => $hasil_pemeliharaan,
                'total_potongan' => $total_potongan,
                'hasil_setelah_potongan' => $hasil_setelah_potongan,
                'total_gaji_pokok' => $total_gaji_pokok,
                'bonus_per_orang' => $bonus_per_orang,
                'total_laba' => $keuntungan_perusahaan,
                'keterangan' => $keterangan,
                'tanggal_perhitungan' => now()
            ]);
    
            // 9. Buat rincian gaji untuk setiap ABK
            $total_pinjaman = 0;
            $total_gaji_bersih = 0;
    
            foreach ($abk_aktif as $abk) {
                // Ambil input pinjaman baru untuk masing-masing ABK, misal input field name: pinjaman_{$abk->id_abk}
                $pinjaman_baru = request()->input("pinjaman_{$abk->id_abk}", 0);
    
                if ($pinjaman_baru > 0) {
                    // Cek apakah sudah ada record pinjaman untuk ABK tersebut pada hari ini
                    $existingPinjaman = Pinjaman::where('abk_id', $abk->id_abk)
                        ->where('ayam_id', $ayam_id)
                        ->where('kandang_id', $kandang_id)
                        ->whereDate('tanggal_pinjaman', now()->toDateString())
                        ->first();
    
                    if ($existingPinjaman) {
                        // Update record yang sudah ada dengan menambah nilai pinjaman baru
                        $existingPinjaman->update([
                            'jumlah_pinjaman' => $existingPinjaman->jumlah_pinjaman + $pinjaman_baru,
                            'updated_at' => now()
                        ]);
                    } else {
                        // Buat record baru
                        Pinjaman::create([
                            'abk_id' => $abk->id_abk,
                            'ayam_id' => $ayam_id,
                            'kandang_id' => $kandang_id,
                            'jumlah_pinjaman' => $pinjaman_baru,
                            'tanggal_pinjaman' => now()
                        ]);
                    }
                }
    
                // Hitung total pinjaman ABK ini di periode & kandang yang sama
                $jumlah_pinjaman = Pinjaman::where('abk_id', $abk->id_abk)
                    ->where('ayam_id', $ayam_id)
                    ->where('kandang_id', $kandang_id)
                    ->sum('jumlah_pinjaman');
    
                // Hitung gaji bersih ABK setelah dikurangi total pinjaman
                $gaji_bersih = $gaji_pokok_per_orang + $bonus_per_orang - $jumlah_pinjaman;
                $total_pinjaman += $jumlah_pinjaman;
                $total_gaji_bersih += $gaji_bersih;
    
                // Simpan rincian gaji ABK
                RincianGajiAbk::create([
                    'perhitungan_id' => $perhitunganGaji->id_perhitungan,
                    'abk_id' => $abk->id_abk,
                    'ayam_id' => $ayam_id,
                    'kandang_id' => $kandang_id,
                    'gaji_pokok' => $gaji_pokok_per_orang,
                    'bonus' => $bonus_per_orang,
                    'jumlah_pinjaman' => $jumlah_pinjaman,
                    'gaji_bersih' => $gaji_bersih
                ]);
            }
    
            DB::commit();
    
            return [
                'perhitungan_gaji' => $perhitunganGaji,
                'rincian_potongan' => $rincian_potongan,
                'summary' => [
                    'hasil_pemeliharaan' => $hasil_pemeliharaan,
                    'total_potongan' => $total_potongan,
                    'hasil_setelah_potongan' => $hasil_setelah_potongan,
                    'perhitungan_gaji_petugas' => [
                        'jumlah_abk_aktif' => $jumlah_abk,
                        'total_gaji_pokok' => $total_gaji_pokok,
                        'gaji_pokok_per_orang' => $gaji_pokok_per_orang,
                        'total_bonus' => $total_bonus,
                        'bonus_per_orang' => $bonus_per_orang,
                        'total_pinjaman' => $total_pinjaman,
                        'total_gaji_bersih' => $total_gaji_bersih,
                        'persentase_gaji' => '20%'
                    ],
                    'perhitungan_akhir' => [
                        'total_pengeluaran' => $total_pengeluaran,
                        'keuntungan_perusahaan' => $keuntungan_perusahaan
                    ]
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    


    public function getSalaryDetails($id_perhitungan)
    {
        return RincianGajiAbk::with(['abk', 'pinjaman'])
            ->where('perhitungan_id', $id_perhitungan)
            ->get();
    }
}