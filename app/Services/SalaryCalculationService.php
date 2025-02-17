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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryCalculationService
{
    public function calculateSalary($ayam_id, $kandang_id, $hasil_pemeliharaan, $bonus_total, $keterangan = 0)
    {
        try {
            DB::beginTransaction();

            // 1. Ambil rincian potongan operasional
            $rincian_potongan = Operasional::where('ayam_id', $ayam_id)
                ->where('kandang_id', $kandang_id)
                ->select('nama_potongan', 'jumlah')
                ->get();

            $total_potongan = $rincian_potongan->sum('jumlah');

            // 2. Hitung hasil setelah potongan
            $hasil_setelah_potongan = $hasil_pemeliharaan - $total_potongan;

            // 3. Hitung jumlah ABK yang active
            $abk_aktif = Abk::where('status', 'active')->get();
            $jumlah_abk = $abk_aktif->count();

            if ($jumlah_abk == 0) {
                throw new \Exception('Tidak ada ABK active yang ditemukan');
            }

            // 4. Hitung gaji pokok per orang (20% dari hasil setelah potongan)
            $total_gaji_pokok = $hasil_setelah_potongan * 0.20;
            $gaji_pokok_per_orang = $total_gaji_pokok / $jumlah_abk;

            // 5. Hitung bonus per orang (jika ada)
            $bonus_per_orang = $bonus_total > 0 ? ($bonus_total / $jumlah_abk) : 0;

            // 6. Buat record perhitungan gaji
            $perhitunganGaji = PerhitunganGaji::create([
                'ayam_id' => $ayam_id,
                'kandang_id' => $kandang_id,
                'hasil_pemeliharaan' => $hasil_pemeliharaan,
                'total_potongan' => $total_potongan,
                'hasil_setelah_potongan' => $hasil_setelah_potongan,
                'total_gaji_pokok' => $total_gaji_pokok,
                'bonus_per_orang' => $bonus_per_orang,
                'keterangan' => $keterangan,
                'tanggal_perhitungan' => now()
            ]);

            // 7. Buat rincian gaji untuk setiap ABK
            $total_bonus = 0;
            $total_pinjaman = 0;
            $total_gaji_bersih = 0;

            foreach ($abk_aktif as $abk) {
                // Cek pinjaman ABK untuk periode ini
                $jumlah_pinjaman = 0;
                
                try {
                    // Cari pinjaman yang ada
                    $pinjaman = Pinjaman::where('abk_id', $abk->id_abk)
                        ->where('ayam_id', $ayam_id)
                        ->where('kandang_id', $kandang_id)
                        ->first();

                    // Jika tidak ada pinjaman, buat record pinjaman kosong
                    if (!$pinjaman) {
                        $pinjaman = Pinjaman::create([
                            'abk_id' => $abk->id_abk,
                            'ayam_id' => $ayam_id,
                            'kandang_id' => $kandang_id,
                            'jumlah_pinjaman' => 0,
                            'tanggal_pinjaman' => now()
                        ]);
                    }

                    $jumlah_pinjaman = $pinjaman->jumlah_pinjaman;
                } catch (\Exception $e) {
                    Log::warning("Error saat mengambil/membuat data pinjaman ABK: " . $e->getMessage());
                    // Buat pinjaman default jika terjadi error
                    $pinjaman = Pinjaman::create([
                        'abk_id' => $abk->id_abk,
                        'ayam_id' => $ayam_id,
                        'kandang_id' => $kandang_id,
                        'jumlah_pinjaman' => 0,
                        'tanggal_pinjaman' => now()
                    ]);
                }

                // Hitung gaji bersih
                $gaji_bersih = $gaji_pokok_per_orang + $bonus_per_orang - $jumlah_pinjaman;

                // Update totals
                $total_bonus += $bonus_per_orang;
                $total_pinjaman += $jumlah_pinjaman;
                $total_gaji_bersih += $gaji_bersih;

                RincianGajiAbk::create([
                    'perhitungan_id' => $perhitunganGaji->id_perhitungan,
                    'abk_id' => $abk->id_abk,
                    'ayam_id' => $ayam_id,
                    'kandang_id' => $kandang_id,
                    'gaji_pokok' => $gaji_pokok_per_orang,
                    'bonus' => $bonus_per_orang,
                    'pinjaman_id' => $pinjaman->id_pinjaman, // Sekarang selalu ada nilai
                    'jumlah_pinjaman' => $jumlah_pinjaman,
                    'gaji_bersih' => $gaji_bersih
                ]);
            }

            // 8. Hitung keuntungan perusahaan
            $total_pengeluaran = $total_potongan + $total_gaji_pokok + $total_bonus;
            $keuntungan_perusahaan = $hasil_pemeliharaan - $total_pengeluaran;

            DB::commit();

            // 9. Return detailed result
            return [
                'perhitungan_gaji' => $perhitunganGaji,
                'rincian_potongan' => $rincian_potongan,
                'summary' => [
                    'hasil_pemeliharaan' => $hasil_pemeliharaan,
                    'rincian_potongan_operasional' => $rincian_potongan->map(function($item) {
                        return [
                            'nama_potongan' => $item->nama_potongan,
                            'jumlah' => $item->jumlah
                        ];
                    }),
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