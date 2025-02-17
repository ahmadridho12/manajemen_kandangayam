<?php
namespace App\Services;

use App\Models\Ayam;
use App\Models\MonitoringPakan;
use App\Models\Pakan;
use App\Models\MonitoringPakanDetail;
use App\Models\PakanMasuk;
use App\Models\PakanKeluar;
use App\Models\PakanTransfer;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringPakanGeneratorService
{
    public function generateFromAyam($id_ayam)
    {
        $ayam = Ayam::findOrFail($id_ayam);
        MonitoringPakan::where('ayam_id', $id_ayam)->delete();
        
        $tanggal_masuk = Carbon::parse($ayam->tanggal_masuk);
        $tanggal_selesai = Carbon::parse($ayam->tanggal_selesai);
        $tanggal_mulai = $tanggal_masuk->copy()->subDays(10);
        
        $current_date = $tanggal_mulai->copy();
        $day = -10;
        
        while ($current_date <= $tanggal_selesai) {
            MonitoringPakan::create([
                'ayam_id' => $id_ayam,
                'tanggal' => $current_date->format('Y-m-d'),
                'day' => $day,
                'total_masuk' => 0,
                'total_berat' => 0,
                'keluar' => 0,
                'sisa' => 0
            ]);
            
            $current_date->addDay();
            $day++;
        }
    }
    
    public function processPakanMasuk(PakanMasuk $pakanMasuk, $isUpdate = false)
    {
        DB::transaction(function() use ($pakanMasuk, $isUpdate) {
            if ($isUpdate) {
                // Hapus semua data lama dulu
                $originalPakanMasuk = PakanMasuk::find($pakanMasuk->id);
                if ($originalPakanMasuk) {
                    // Debug untuk cek nilai
                    Log::info('Original data:', [
                        'masuk' => $originalPakanMasuk->masuk,
                        'total_berat' => $originalPakanMasuk->total_berat
                    ]);
                    
                    $this->reversePakanMasuk($originalPakanMasuk);
                }
            }
    
            // Debug untuk cek nilai baru
            Log::info('New data:', [
                'masuk' => $pakanMasuk->masuk,
                'total_berat' => $pakanMasuk->total_berat
            ]);
    
            // Update MonitoringPakanDetail dengan nilai baru
            $existingDetail = MonitoringPakanDetail::where('pakan_id', $pakanMasuk->pakan_id)
                                                 ->where('ayam_id', $pakanMasuk->ayam_id)
                                                 ->first();
            
            if ($existingDetail) {
                // Update langsung dengan nilai baru (bukan increment)
                $existingDetail->update([
                    'masuk' => $pakanMasuk->masuk,
                    'berat_zak' => $pakanMasuk->berat_zak,
                    'total_berat' => $pakanMasuk->masuk * $pakanMasuk->berat_zak
                ]);
            } else {
                MonitoringPakanDetail::create([
                    'ayam_id' => $pakanMasuk->ayam_id,
                    'pakan_id' => $pakanMasuk->pakan_id,
                    'masuk' => $pakanMasuk->masuk,
                    'berat_zak' => $pakanMasuk->berat_zak,
                    'total_berat' => $pakanMasuk->masuk * $pakanMasuk->berat_zak
                ]);
            }
    
            // Update monitoring harian
            $dateStr = Carbon::parse($pakanMasuk->tanggal)->format('Y-m-d');
            $monitoring = MonitoringPakan::where('ayam_id', $pakanMasuk->ayam_id)
                                       ->whereDate('tanggal', $dateStr)
                                       ->first();
                                       
            if (!$monitoring) {
                throw new \Exception('Data monitoring tidak ditemukan untuk tanggal ' . $dateStr);
            }
    
            // Update langsung dengan nilai baru (bukan increment)
            $monitoring->update([
                'total_masuk' => $pakanMasuk->masuk,
                'total_berat' => $pakanMasuk->masuk * $pakanMasuk->berat_zak
            ]);
    
            $this->updateSisaPakan($pakanMasuk->ayam_id, $dateStr);
        });
    }
    
    private function reversePakanMasuk(PakanMasuk $originalPakanMasuk)
    {
        // Reset detail ke 0 dulu
        $detail = MonitoringPakanDetail::where('pakan_id', $originalPakanMasuk->pakan_id)
                                     ->where('ayam_id', $originalPakanMasuk->ayam_id)
                                     ->first();
    
        if ($detail) {
            $detail->update([
                'masuk' => 0,
                'total_berat' => 0
            ]);
        }
    
        // Reset monitoring ke 0 dulu
        $dateStr = Carbon::parse($originalPakanMasuk->tanggal)->format('Y-m-d');
        $monitoring = MonitoringPakan::where('ayam_id', $originalPakanMasuk->ayam_id)
                                   ->whereDate('tanggal', $dateStr)
                                   ->first();
    
        if ($monitoring) {
            $monitoring->update([
                'keluar' => 0,
                'total_berat' => 0
            ]);
        }
    }


    // ProcessPakanKeluar.php
    public function processPakanKeluar(PakanKeluar $pakanKeluar, $isUpdate = false)
{
   DB::transaction(function() use ($pakanKeluar, $isUpdate) {
       // Variabel untuk menyimpan kuantitas asli
       $originalQty = 0;

       // Jika sedang proses update
       if ($isUpdate) {
           $originalPakanKeluar = PakanKeluar::find($pakanKeluar->id);
           if ($originalPakanKeluar) {
               // Simpan kuantitas asli sebelum diupdate
               $originalQty = $originalPakanKeluar->qty;

               // Kembalikan stok sebelumnya
               $this->reversePakanKeluar($originalPakanKeluar); 
           }
       }

       // Cari detail monitoring pakan yang sesuai
       $existingDetail = MonitoringPakanDetail::where('pakan_id', $pakanKeluar->pakan_id)
           ->where('ayam_id', $pakanKeluar->ayam_id)
           ->lockForUpdate()
           ->first();

       if ($existingDetail) {
           // Hitung ulang stok masuk
           // Kurangi qty baru dan tambahkan kembali qty asli
           $new_masuk = $existingDetail->masuk - ($pakanKeluar->qty - $originalQty);

           
           $existingDetail->update([
               'masuk' => $new_masuk,
               'berat_zak' => $pakanKeluar->berat_zak,
               'total_berat' => $new_masuk * $pakanKeluar->berat_zak,
               'updated_at' => now()
           ]);
       
    
               // Log setelah update
               Log::info('Detail setelah update:', [
                   'masuk_after' => $existingDetail->fresh()->masuk
               ]);
           } else {
               MonitoringPakanDetail::create([
                   'ayam_id' => $pakanKeluar->ayam_id,
                   'pakan_id' => $pakanKeluar->pakan_id,
                   'masuk' => $pakanKeluar->masuk,
                   'berat_zak' => $pakanKeluar->berat_zak, 
                   'total_berat' => $pakanKeluar->masuk * $pakanKeluar->berat_zak,
                   'created_at' => now(),
                   'updated_at' => now()
               ]);
           }
    
           // Update monitoring harian
           $dateStr = Carbon::parse($pakanKeluar->tanggal)->format('Y-m-d');
           $monitoring = MonitoringPakan::where('ayam_id', $pakanKeluar->ayam_id)
               ->whereDate('tanggal', $dateStr)
               ->lockForUpdate() // Tambahkan lock
               ->first();
                                      
           if (!$monitoring) {
               throw new \Exception('Data monitoring tidak ditemukan untuk tanggal ' . $dateStr);
           }
    
           // Update langsung dengan nilai baru
           $monitoring->update([
               'keluar' => $pakanKeluar->qty,
               'updated_at' => now()
           ]);
    
           $this->updateSisaPakan($pakanKeluar->ayam_id, $dateStr);
       });
    }
    
    private function reversePakanKeluar(PakanKeluar $originalPakanKeluar)
    {
       $detail = MonitoringPakanDetail::where('pakan_id', $originalPakanKeluar->pakan_id)
           ->where('ayam_id', $originalPakanKeluar->ayam_id)
           ->lockForUpdate()
           ->first();
    
       if ($detail) {
           // Kembalikan qty asli
           $new_masuk = $detail->masuk + $originalPakanKeluar->qty;
           $detail->update([
               'masuk' => $new_masuk,
               'total_berat' => $new_masuk * $detail->berat_zak,
               'updated_at' => now()
           ]);
       }
    
       // Reset monitoring harian
       $dateStr = Carbon::parse($originalPakanKeluar->tanggal)->format('Y-m-d');
       $monitoring = MonitoringPakan::where('ayam_id', $originalPakanKeluar->ayam_id)
           ->whereDate('tanggal', $dateStr)
           ->lockForUpdate()
           ->first();
    
       if ($monitoring) {
           $monitoring->update([
               'keluar' => 0,
               'updated_at' => now()
           ]);
       }
    }

    // public function processPakanKeluar(PakanKeluar $pakanKeluar, $isUpdate = false)
    // {
    //     DB::transaction(function() use ($pakanKeluar, $isUpdate) {
    //         // Tambahkan log untuk debugging
    //         Log::info('Memulai proses pakan keluar', [
    //             'pakan_id' => $pakanKeluar->pakan_id,
    //             'ayam_id' => $pakanKeluar->ayam_id,
    //             'qty' => $pakanKeluar->qty,
    //             'is_update' => $isUpdate
    //         ]);

    //         $detail = MonitoringPakanDetail::where('pakan_id', $pakanKeluar->pakan_id)
    //             ->where('ayam_id', $pakanKeluar->ayam_id)
    //             ->lockForUpdate()  // Tambahkan lock untuk mencegah race condition
    //             ->first();

    //         if (!$detail) {
    //             throw new \Exception('Data monitoring detail tidak ditemukan');
    //         }

    //         Log::info('Stok awal', ['masuk' => $detail->masuk]);

    //         if ($isUpdate) {
    //             // Ambil data original sebelum update
    //             $originalPakanKeluar = PakanKeluar::find($pakanKeluar->id);
                
    //             if ($originalPakanKeluar) {
    //                 Log::info('Data original', [
    //                     'qty_lama' => $originalPakanKeluar->qty,
    //                     'qty_baru' => $pakanKeluar->qty
    //                 ]);

    //                 // Hitung selisih antara qty lama dan qty baru
    //                 $selisih = $pakanKeluar->qty - $originalPakanKeluar->qty;
                    
    //                 Log::info('Selisih qty', ['selisih' => $selisih]);

    //                 // Jika selisih positif, berarti qty baru lebih besar (kurangi stok)
    //                 // Jika selisih negatif, berarti qty baru lebih kecil (tambah stok)
    //                 $new_masuk = $detail->masuk - $selisih;

    //                 if ($new_masuk < 0) {
    //                     throw new \Exception('Stok tidak mencukupi. Stok saat ini: ' . $detail->masuk);
    //                 }

    //                 Log::info('Stok baru', ['new_masuk' => $new_masuk]);

    //                 // Update detail monitoring
    //                 $detail->update([
    //                     'masuk' => $new_masuk,
    //                     'total_berat' => $new_masuk * $detail->berat_zak
    //                 ]);
    //             }
    //         } else {
    //             // Proses pakan keluar baru
    //             if ($detail->masuk < $pakanKeluar->qty) {
    //                 throw new \Exception('Stok tidak mencukupi. Stok saat ini: ' . $detail->masuk);
    //             }

    //             $new_masuk = $detail->masuk - $pakanKeluar->qty;
                
    //             $detail->update([
    //                 'masuk' => $new_masuk,
    //                 'total_berat' => $new_masuk * $detail->berat_zak
    //             ]);
    //         }

    //         // Update monitoring harian
    //         $dateStr = Carbon::parse($pakanKeluar->tanggal)->format('Y-m-d');
    //         $monitoring = MonitoringPakan::where('ayam_id', $pakanKeluar->ayam_id)
    //             ->whereDate('tanggal', $dateStr)
    //             ->first();

    //         if (!$monitoring) {
    //             throw new \Exception('Data monitoring tidak ditemukan untuk tanggal ' . $dateStr);
    //         }

    //         $monitoring->update([
    //             'masuk' => $pakanKeluar->qty,
    //             'total_berat' => $pakanKeluar->qty * $pakanKeluar->berat_zak
    //         ]);

    //         Log::info('Proses selesai', [
    //             'stok_akhir' => $detail->fresh()->masuk
    //         ]);
    //     });
    // }


    public function transfer(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Cek stok di kandang asal
            $stokAsal = MonitoringPakanDetail::where('ayam_id', $data['ayam_asal_id'])
                                           ->where('pakan_id', $data['pakan_id'])
                                           ->first();

            if (!$stokAsal || $stokAsal->masuk < $data['qty']) {
                throw new \Exception('Stok pakan di kandang asal tidak mencukupi');
            }

            // Buat record transfer
            $transfer = PakanTransfer::create([
                'tanggal' => $data['tanggal'],
                'kandang_asal_id' => $data['kandang_asal_id'],
                'kandang_tujuan_id' => $data['kandang_tujuan_id'],
                'ayam_asal_id' => $data['ayam_asal_id'],
                'ayam_tujuan_id' => $data['ayam_tujuan_id'],
                'pakan_id' => $data['pakan_id'],
                'qty' => $data['qty'],
                'berat_zak' => $data['berat_zak'],
                'total_berat' => $data['qty'] * $data['berat_zak'],
                'keterangan' => $data['keterangan'] ?? null
            ]);

            // Kurangi stok di kandang asal
            $new_masuk_asal = $stokAsal->masuk - $data['qty'];
            $new_total_berat_asal = $new_masuk_asal * $stokAsal->berat_zak;
            
            $stokAsal->update([
                'masuk' => $new_masuk_asal,
                'total_berat' => $new_total_berat_asal
            ]);

            // Tambah stok di kandang tujuan
            $stokTujuan = MonitoringPakanDetail::where('ayam_id', $data['ayam_tujuan_id'])
                                             ->where('pakan_id', $data['pakan_id'])
                                             ->first();

            if ($stokTujuan) {
                $stokTujuan->increment('masuk', $data['qty']);
                $stokTujuan->increment('total_berat', $data['qty'] * $data['berat_zak']);
            } else {
                MonitoringPakanDetail::create([
                    'ayam_id' => $data['ayam_tujuan_id'],
                    'pakan_id' => $data['pakan_id'],
                    'masuk' => $data['qty'],
                    'berat_zak' => $data['berat_zak'],
                    'total_berat' => $data['qty'] * $data['berat_zak']
                ]);
            }

            // Update monitoring harian untuk kedua kandang
            $this->updateSisaPakan($data['ayam_asal_id'], $data['tanggal']);
            $this->updateSisaPakan($data['ayam_tujuan_id'], $data['tanggal']);

            return $transfer;
        });
    }
    private function updateSisaPakan($ayam_id, $tanggal)
    {
        // Ambil semua data monitoring dari tanggal yang ditentukan ke depan
        $monitorings = MonitoringPakan::where('ayam_id', $ayam_id)
                                     ->whereDate('tanggal', '>=', $tanggal)
                                     ->orderBy('tanggal')
                                     ->get();
        
        // Ambil sisa pakan dari hari sebelumnya
        $previousSisa = MonitoringPakan::where('ayam_id', $ayam_id)
                                      ->whereDate('tanggal', '<', $tanggal)
                                      ->orderBy('tanggal', 'desc')
                                      ->value('sisa') ?? 0;
        
        // Mulai perhitungan dari sisa sebelumnya
        $running_sisa = $previousSisa;
        
        foreach ($monitorings as $monitoring) {
            // Hitung sisa untuk hari ini:
            // 1. Gunakan sisa dari perhitungan sebelumnya
            // 2. Tambahkan pakan yang masuk hari ini
            // 3. Kurangi pakan yang keluar hari ini
            $sisa_hari_ini = $running_sisa + $monitoring->total_masuk - ($monitoring->keluar ?? 0);
            
            // Update sisa di database
            $monitoring->update(['sisa' => $sisa_hari_ini]);
            
            // Set running_sisa untuk perhitungan hari berikutnya
            $running_sisa = $sisa_hari_ini;
        }
    }
}