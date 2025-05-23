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
    public function generateFromAyam(int $id_ayam)
    {
        $ayam = Ayam::findOrFail($id_ayam);
        $startDate = Carbon::parse($ayam->tanggal_masuk)->subDays(10);
        $endDate   = Carbon::parse($ayam->tanggal_selesai);
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Calculate day offset
            $day = $startDate->diffInDays($currentDate) - 10;

            $attributes = [
                'ayam_id' => $id_ayam,
                'tanggal' => $currentDate->toDateString(),
            ];
            $values = [
                'day'            => $day,
                'total_masuk'    => 0,
                'total_berat'    => 0,
                'keluar'         => 0,
                'sisa'           => 0,
                'transfer_id'    => null,
                'total_transfer' => 0,
            ];
            MonitoringPakan::updateOrCreate($attributes, $values);
            $currentDate->addDay();
        }
    }

    /**
     * Delta generate for updates: adds or removes only the needed records
     */
    public function generateDeltaFromAyam(
        int $id_ayam,
        Carbon $oldStart,
        Carbon $oldEnd,
        Carbon $newStart,
        Carbon $newEnd
    ) {
        // Determine new begin including pre-feed
        $newBegin = $newStart->copy()->subDays(10);
        $oldBegin = $oldStart->copy()->subDays(10);

        // 1) Remove records beyond new end date
        MonitoringPakan::where('ayam_id', $id_ayam)
            ->whereDate('tanggal', '>', $newEnd->toDateString())
            ->delete();

        // 2) Add missing records before old begin if new begin is earlier
        if ($newBegin->lt($oldBegin)) {
            $this->insertRange($id_ayam, $newBegin, $oldBegin->subDay(), $newBegin);
        }

        // 3) Add missing records after old end if new end is later
        if ($newEnd->gt($oldEnd)) {
            $this->insertRange($id_ayam, $oldEnd->addDay(), $newEnd, $newBegin);
        }

        // 4) Re-index 'day' for all existing records within the new range
        $this->reindexDays($id_ayam, $newBegin);
    }

    /**
     * Insert default records between two dates inclusive
     */
    protected function insertRange(int $id_ayam, Carbon $from, Carbon $to, Carbon $baseBegin)
    {
        $current = $from->copy();
        while ($current->lte($to)) {
            $day = $baseBegin->diffInDays($current) - 10;

            MonitoringPakan::updateOrCreate(
                ['ayam_id' => $id_ayam, 'tanggal' => $current->toDateString()],
                [
                    'day'            => $day,
                    'total_masuk'    => 0,
                    'total_berat'    => 0,
                    'keluar'         => 0,
                    'sisa'           => 0,
                    'transfer_id'    => null,
                    'total_transfer' => 0,
                ]
            );
            $current->addDay();
        }
    }

    /**
     * Recalculate 'day' offset based on a new begin date
     */
    protected function reindexDays(int $id_ayam, Carbon $begin)
    {
        $records = MonitoringPakan::where('ayam_id', $id_ayam)
            ->whereDate('tanggal', '>=', $begin->toDateString())
            ->orderBy('tanggal')
            ->get();

        foreach ($records as $index => $record) {
            $record->day = $index - 10;
            $record->save();
        }
    }

    public function processPakanMasuk(PakanMasuk $pakanMasuk, $isUpdate = false, $oldMasuk = 0, $oldBeratZak = 0)
    {
        DB::transaction(function() use ($pakanMasuk, $isUpdate, $oldMasuk, $oldBeratZak) {
            $tanggalStr = Carbon::parse($pakanMasuk->tanggal)->format('Y-m-d');
            
            // 1. Proses untuk MonitoringPakanDetail
            $detailPakan = MonitoringPakanDetail::where('pakan_id', $pakanMasuk->pakan_id)
                                                  ->where('ayam_id', $pakanMasuk->ayam_id)
                                                  ->first();
            
            // Hitung nilai masuk dan berat zak baru
            $selisihMasuk = $pakanMasuk->masuk;
            $selisihBerat = $pakanMasuk->masuk * $pakanMasuk->berat_zak;
            
            if ($isUpdate) {
                // Gunakan nilai original yang sudah disimpan sebelum update
                $selisihMasuk = $pakanMasuk->masuk - $oldMasuk;
                $selisihBerat = ($pakanMasuk->masuk * $pakanMasuk->berat_zak) - ($oldMasuk * $oldBeratZak);
                
                if ($detailPakan) {
                    $detailPakan->update([
                        'masuk'       => $detailPakan->masuk + $selisihMasuk,
                        'berat_zak'   => $pakanMasuk->berat_zak,
                        'total_berat' => $detailPakan->total_berat + $selisihBerat
                    ]);
                }
            } else {
                // Untuk data baru
                if ($detailPakan) {
                    $detailPakan->update([
                        'masuk'       => $detailPakan->masuk + $pakanMasuk->masuk,
                        'berat_zak'   => $pakanMasuk->berat_zak,
                        'total_berat' => $detailPakan->total_berat + ($pakanMasuk->masuk * $pakanMasuk->berat_zak)
                    ]);
                } else {
                    MonitoringPakanDetail::create([
                        'ayam_id'     => $pakanMasuk->ayam_id,
                        'pakan_id'    => $pakanMasuk->pakan_id,
                        'masuk'       => $pakanMasuk->masuk,
                        'berat_zak'   => $pakanMasuk->berat_zak,
                        'total_berat' => $pakanMasuk->masuk * $pakanMasuk->berat_zak
                    ]);
                }
            }
            
            // 2. Update data monitoring untuk tanggal spesifik
            $monitoring = MonitoringPakan::where('ayam_id', $pakanMasuk->ayam_id)
                                         ->whereDate('tanggal', $tanggalStr)
                                         ->first();
            
            if (!$monitoring) {
                throw new \Exception('Data monitoring tidak ditemukan untuk tanggal ' . $tanggalStr);
            }
            
            // Update total_masuk dan total_berat untuk tanggal ini saja
            $monitoring->update([
                'total_masuk' => $monitoring->total_masuk + $selisihMasuk,
                'total_berat' => $monitoring->total_berat + $selisihBerat
            ]);
            
            // 3. Update nilai sisa untuk tanggal ini dan seterusnya
            $this->updateAllSisaPakanAfterDate($pakanMasuk->ayam_id, $tanggalStr);
        });
    }
    
    // ProcessPakanKeluar.php
    public function processPakanKeluar(PakanKeluar $pakanKeluar, $isUpdate = false, $oldQty = 0)
    {
       DB::transaction(function() use ($pakanKeluar, $isUpdate, $oldQty) {
           // Cari detail monitoring pakan yang sesuai
           $existingDetail = MonitoringPakanDetail::where('pakan_id', $pakanKeluar->pakan_id)
               ->where('ayam_id', $pakanKeluar->ayam_id)
               ->lockForUpdate()
               ->first();
    
           if ($existingDetail) {
               // Hitung selisih: jika update, gunakan selisih antara new qty dan oldQty; 
               // jika create, gunakan qty baru
               $difference = $isUpdate ? ($pakanKeluar->qty - $oldQty) : $pakanKeluar->qty;
               // Update stok (masuk) dengan mengurangi selisih
               $new_masuk = $existingDetail->masuk - $difference;
    
               $existingDetail->update([
                   'masuk'       => $new_masuk,
                   'berat_zak'   => $pakanKeluar->berat_zak,
                   'total_berat' => $new_masuk * $pakanKeluar->berat_zak,
                   'updated_at'  => now()
               ]);
    
               Log::info('Detail setelah update:', [
                   'masuk_after' => $existingDetail->fresh()->masuk
               ]);
           } else {
               MonitoringPakanDetail::create([
                   'ayam_id'     => $pakanKeluar->ayam_id,
                   'pakan_id'    => $pakanKeluar->pakan_id,
                   'masuk'       => $pakanKeluar->qty,
                   'berat_zak'   => $pakanKeluar->berat_zak,
                   'total_berat' => $pakanKeluar->qty * $pakanKeluar->berat_zak,
                   'created_at'  => now(),
                   'updated_at'  => now()
               ]);
           }
        
           // Update monitoring harian
           $dateStr = Carbon::parse($pakanKeluar->tanggal)->format('Y-m-d');
           $monitoring = MonitoringPakan::where('ayam_id', $pakanKeluar->ayam_id)
               ->whereDate('tanggal', $dateStr)
               ->lockForUpdate()
               ->first();
                                          
           if (!$monitoring) {
               throw new \Exception('Data monitoring tidak ditemukan untuk tanggal ' . $dateStr);
           }
        
           // Hitung total keluar (qty) dari semua record PakanKeluar pada tanggal tersebut
           $newKeluar = PakanKeluar::where('ayam_id', $pakanKeluar->ayam_id)
               ->whereDate('tanggal', $dateStr)
               ->sum('qty');
        
           $monitoring->update([
               'keluar'     => $newKeluar,
               'updated_at' => now()
           ]);
        
           $this->updateSisaPakan($pakanKeluar->ayam_id, $dateStr);
       });
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
        // 1. Cek stok di kandang asal (MonitoringPakanDetail)
        $stokAsal = MonitoringPakanDetail::where('ayam_id', $data['ayam_asal_id'])
                                           ->where('pakan_id', $data['pakan_id'])
                                           ->first();

        if (!$stokAsal || $stokAsal->masuk < $data['qty']) {
            throw new \Exception('Stok pakan di kandang asal tidak mencukupi');
        }

        // 2. Buat record transfer di tabel pakan_transfers
        $transfer = PakanTransfer::create([
            'tanggal'           => $data['tanggal'],
            'kandang_asal_id'   => $data['kandang_asal_id'],
            'kandang_tujuan_id' => $data['kandang_tujuan_id'],
            'ayam_asal_id'      => $data['ayam_asal_id'],
            'ayam_tujuan_id'    => $data['ayam_tujuan_id'],
            'pakan_id'          => $data['pakan_id'],
            'qty'               => $data['qty'],
            'berat_zak'         => $data['berat_zak'],
            'total_berat'       => $data['qty'] * $data['berat_zak'],
            'keterangan'        => $data['keterangan'] ?? null
        ]);

        // 3. Kurangi stok di kandang asal (MonitoringPakanDetail)
        $new_masuk_asal = $stokAsal->masuk - $data['qty'];
        $new_total_berat_asal = $new_masuk_asal * $stokAsal->berat_zak;
        $stokAsal->update([
            'masuk'       => $new_masuk_asal,
            'total_berat' => $new_total_berat_asal
        ]);

        // 4. Tambah stok di kandang tujuan (MonitoringPakanDetail)
        $stokTujuan = MonitoringPakanDetail::where('ayam_id', $data['ayam_tujuan_id'])
                                           ->where('pakan_id', $data['pakan_id'])
                                           ->first();
        if ($stokTujuan) {
            $stokTujuan->increment('masuk', $data['qty']);
            $stokTujuan->increment('total_berat', $data['qty'] * $data['berat_zak']);
        } else {
            MonitoringPakanDetail::create([
                'ayam_id'     => $data['ayam_tujuan_id'],
                'pakan_id'    => $data['pakan_id'],
                'masuk'       => $data['qty'],
                'berat_zak'   => $data['berat_zak'],
                'total_berat' => $data['qty'] * $data['berat_zak']
            ]);
        }

        // 5. Pastikan record monitoring pakan harian untuk tanggal transfer ada
        //    untuk ayam asal dan ayam tujuan.
        $monitoringAsal = MonitoringPakan::firstOrCreate(
            ['ayam_id' => $data['ayam_asal_id'], 'tanggal' => $data['tanggal']],
            ['day' => 0, 'total_masuk' => 0, 'total_berat' => 0, 'keluar' => 0, 'sisa' => 0]
        );
        $monitoringTujuan = MonitoringPakan::firstOrCreate(
            ['ayam_id' => $data['ayam_tujuan_id'], 'tanggal' => $data['tanggal']],
            ['day' => 0, 'total_masuk' => 0, 'total_berat' => 0, 'keluar' => 0, 'sisa' => 0]
        );

        // 6. Update record monitoring pakan untuk mencatat transfer.
        // Hanya untuk ayam tujuan, kita akumulasi total_transfer dengan nilai transfer baru.
        $newTransferTujuan = ($monitoringTujuan->total_transfer ?? 0) + $data['qty'];
        $monitoringTujuan->update([
            'transfer_id'    => $transfer->id,
            'total_transfer' => $newTransferTujuan
        ]);
        // Untuk ayam asal, jika kamu tidak ingin mencatat transfer keluar, biarkan total_transfer tetap 0.
        $monitoringAsal->update([
            'transfer_id'    => $transfer->id,
            'total_transfer' => 0
        ]);

        // 7. Update sisa pakan untuk kedua ayam mulai dari tanggal transfer.
        $this->updateSisaPakan($data['ayam_asal_id'], $data['tanggal']);
        $this->updateSisaPakan($data['ayam_tujuan_id'], $data['tanggal']);

        return $transfer;
    });
}

public function rollbackPakanKeluar(PakanKeluar $pakanKeluar)
{
    $dateStr = Carbon::parse($pakanKeluar->tanggal)->format('Y-m-d');
    
    // Hitung total qty pakan keluar yang tersisa (kecuali record yang dihapus)
    $remainingQty = PakanKeluar::where('ayam_id', $pakanKeluar->ayam_id)
                        ->whereDate('tanggal', $dateStr)
                        ->where('id', '<>', $pakanKeluar->id) // pastikan mengecualikan record yang akan dihapus
                        ->sum('qty');
    
    // Update data monitoring pakan untuk tanggal tersebut
    $monitoring = MonitoringPakan::where('ayam_id', $pakanKeluar->ayam_id)
                        ->whereDate('tanggal', $dateStr)
                        ->first();
    
    if ($monitoring) {
        $monitoring->update([
            'keluar'     => $remainingQty,
            'updated_at' => now()
        ]);
    }
    
    // Update sisa pakan agar konsisten
    $this->updateSisaPakan($pakanKeluar->ayam_id, $dateStr);
}

    
    // Fungsi untuk mengupdate sisa pakan untuk semua tanggal setelah perubahan
    private function updateAllSisaPakanAfterDate($ayamId, $startDate)
    {
        // Ambil semua data monitoring setelah tanggal startDate (termasuk tanggal startDate)
        $monitorings = MonitoringPakan::where('ayam_id', $ayamId)
                                     ->whereDate('tanggal', '>=', $startDate)
                                     ->orderBy('tanggal')
                                     ->get();
        
        $runningSisa = 0;
        
        // Untuk tanggal pertama, ambil sisa dari tanggal sebelumnya jika ada
        $previousMonitoring = MonitoringPakan::where('ayam_id', $ayamId)
                                            ->whereDate('tanggal', '<', $startDate)
                                            ->orderBy('tanggal', 'desc')
                                            ->first();
        
        if ($previousMonitoring) {
            $runningSisa = $previousMonitoring->sisa;
        }
        
        foreach ($monitorings as $monitoring) {
            // Hitung sisa baru: sisa sebelumnya + masuk - keluar
            $newSisa = $runningSisa + $monitoring->total_masuk - $monitoring->keluar;
            
            // Update sisa di database
            $monitoring->update(['sisa' => $newSisa]);
            
            // Perbarui running sisa untuk iterasi berikutnya
            $runningSisa = $newSisa;
        }
    }
   // Ubah dari private menjadi public
public function updateSisaPakan($ayam_id, $tanggal)
{
    // Ambil semua data monitoring dari tanggal yang ditentukan ke depan (urut ascending)
    $monitorings = MonitoringPakan::where('ayam_id', $ayam_id)
                                  ->whereDate('tanggal', '>=', $tanggal)
                                  ->orderBy('tanggal', 'asc')
                                  ->get();

    // Ambil sisa pakan dari hari sebelumnya
    $previousSisa = MonitoringPakan::where('ayam_id', $ayam_id)
                                   ->whereDate('tanggal', '<', $tanggal)
                                   ->orderBy('tanggal', 'desc')
                                   ->value('sisa') ?? 0;

    // Mulai perhitungan dari sisa sebelumnya
    $running_sisa = $previousSisa;

    foreach ($monitorings as $monitoring) {
        // Gunakan nilai total_transfer langsung dari objek monitoring (jika ada)
        $fresh_total_transfer = (int) ($monitoring->total_transfer ?? 0);

        // Hitung sisa hari ini dengan rumus: sisa = running_sisa + total_masuk + total_transfer - keluar
        $sisa_hari_ini = $running_sisa 
                         + ($monitoring->total_masuk ?? 0)
                         + $fresh_total_transfer 
                         - ($monitoring->keluar ?? 0);

        // Update record monitoring dengan sisa yang baru dihitung
        $monitoring->update(['sisa' => $sisa_hari_ini]);

        // Set running_sisa untuk perhitungan hari berikutnya
        $running_sisa = $sisa_hari_ini;
    }
}

    
    
}