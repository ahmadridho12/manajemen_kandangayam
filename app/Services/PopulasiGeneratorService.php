<?php
namespace App\Services;

use App\Models\Ayam;
use App\Models\AyamMati;
use App\Models\Panen;
use App\Models\Populasi;
use Illuminate\Support\Carbon;

class PopulasiGeneratorService {
    public function generateFromAyam($ayamId) {
        $ayam = Ayam::findOrFail($ayamId);
        
        Populasi::where('populasi', $ayamId)->delete();

        $startDate = Carbon::parse($ayam->tanggal_masuk);
        $endDate = Carbon::parse($ayam->tanggal_selesai);
        $totalDays = $startDate->diffInDays($endDate);
    
        $runningTotal = $ayam->qty_ayam;
    
        for ($day = 0; $day <= $totalDays; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
    
            // Get all ayam mati for current date
            $ayamMati = AyamMati::where('ayam_id', $ayam->id_ayam)
                ->whereDate('tanggal_mati', $currentDate)
                ->first();
    
            // Get all panen for current date
            $panens = Panen::where('ayam_id', $ayam->id_ayam)
                ->whereDate('tanggal_panen', $currentDate)
                ->get();
    
            $qtyMati = $ayamMati ? $ayamMati->quantity_mati : 0;
            
            // Sum all panen quantities for this date
            $qtyPanen = $panens->sum('quantity');
    
            $populasi = new Populasi();
            $populasi->populasi = $ayam->id_ayam;
            $populasi->day = $day;
            $populasi->tanggal = $currentDate;
            $populasi->qty_now = $runningTotal;
            
            // Set foreign keys
            $populasi->mati = $ayamMati ? $ayamMati->id_ayam_mati : null;
            
            // Store all panen IDs as JSON if there are multiple panens
            if ($panens->count() > 0) {
                $populasi->panen = $panens->count() == 1 ? 
                    $panens->first()->id_panen : 
                    $panens->pluck('id_panen')->toJson();
            } else {
                $populasi->panen = null;
            }
            
            $populasi->qty_mati = $qtyMati;
            $populasi->qty_panen = $qtyPanen;
            $populasi->total = $runningTotal - ($qtyMati + $qtyPanen);
            
            $runningTotal = $populasi->total;
    
            $populasi->save();
        }
    }
    
    public function updatePopulasiByAyamMati(AyamMati $ayamMati) {
        $tanggalMati = Carbon::parse($ayamMati->tanggal_mati);
    
        // Update the current day's record
        $populasi = Populasi::where('populasi', $ayamMati->ayam_id)
            ->whereDate('tanggal', $tanggalMati->toDateString())
            ->first();
    
        if ($populasi) {
            // Set the foreign key to id_ayam_mati instead of boolean
            $populasi->mati = $ayamMati->id_ayam_mati;
            $populasi->qty_mati = $ayamMati->quantity_mati;
            $populasi->total = $populasi->qty_now - ($populasi->qty_mati + $populasi->qty_panen);
            $populasi->save();
    
            // Update all subsequent days
            $this->updateSubsequentDays($ayamMati->ayam_id, $tanggalMati);
        }
    }
    
    public function updatePopulasiByPanen(Panen $panen) {
        $tanggalPanen = Carbon::parse($panen->tanggal_panen);
    
        $populasi = Populasi::where('populasi', $panen->ayam_id)
            ->whereDate('tanggal', $tanggalPanen->toDateString())
            ->first();
    
        if ($populasi) {
            // Get all panens for this date
            $allPanens = Panen::where('ayam_id', $panen->ayam_id)
                ->whereDate('tanggal_panen', $tanggalPanen)
                ->get();
            
            // Calculate total panen quantity
            $totalPanenQuantity = $allPanens->sum('quantity');
            
            // Sync panen relations
            $populasi->panens()->sync($allPanens->pluck('id_panen'));
            
            // Update quantities
            $populasi->qty_panen = $totalPanenQuantity;
            $populasi->total = $populasi->qty_now - ($populasi->qty_mati + $totalPanenQuantity);
            $populasi->save();
    
            $this->updateSubsequentDays($panen->ayam_id, $tanggalPanen);
        }
    }
    
    public function rollbackPopulasiByPanen(Panen $panen)
    {
        $tanggalPanen = Carbon::parse($panen->tanggal_panen)->toDateString();
    
        // Ambil record populasi untuk ayam tersebut pada tanggal panen
        $populasiRecord = Populasi::where('populasi', $panen->ayam_id)
            ->whereDate('tanggal', $tanggalPanen)
            ->first();
    
        if ($populasiRecord) {
            // Karena panen dihapus, qty_panen di hari tersebut di-set ke 0
            $populasiRecord->qty_panen = 0;
            // qty_now tidak berubah (mengacu pada nilai total dari hari sebelumnya)
            $populasiRecord->total = $populasiRecord->qty_now - ($populasiRecord->qty_mati + $populasiRecord->qty_panen);
            $populasiRecord->save();
        }
    
        // Update record pada hari-hari berikutnya agar konsisten
        $this->updateSubsequentDays($panen->ayam_id, $tanggalPanen);
    }
    
    public function rollbackPopulasiByAyamMati(AyamMati $ayamMati)
{
    // Ambil tanggal ayam mati yang ingin di-rollback
    $tanggalMati = Carbon::parse($ayamMati->tanggal_mati)->toDateString();

    // Cari record populasi untuk ayam tersebut pada tanggal mati
    $populasiRecord = Populasi::where('populasi', $ayamMati->ayam_id)
        ->whereDate('tanggal', $tanggalMati)
        ->first();

    if ($populasiRecord) {
        // Karena data ayam mati dihapus, set qty_mati ke 0
        $populasiRecord->qty_mati = 0;
        // Total dihitung ulang: total = qty_now - (qty_mati + qty_panen)
        $populasiRecord->total = $populasiRecord->qty_now - ($populasiRecord->qty_mati + $populasiRecord->qty_panen);
        $populasiRecord->save();
    }

    // Update record pada hari-hari berikutnya agar perhitungannya konsisten
    $this->updateSubsequentDays($ayamMati->ayam_id, $tanggalMati);
}


    private function updateSubsequentDays($ayamId, $startDate)
    {
        // Ambil record hari berikutnya secara urut berdasarkan tanggal
        $subsequentRecords = Populasi::where('populasi', $ayamId)
            ->whereDate('tanggal', '>', $startDate)
            ->orderBy('tanggal')
            ->get();
    
        // Ambil total pada tanggal mulai sebagai dasar untuk qty_now hari berikutnya
        $previousTotal = Populasi::where('populasi', $ayamId)
            ->whereDate('tanggal', $startDate)
            ->value('total');
    
        foreach ($subsequentRecords as $record) {
            // Set qty_now berdasarkan total dari hari sebelumnya
            $record->qty_now = $previousTotal;
            // Total dihitung ulang: total = qty_now - (qty_mati + qty_panen)
            $record->total = $record->qty_now - ($record->qty_mati + $record->qty_panen);
            $record->save();
    
            // Update previousTotal untuk record berikutnya
            $previousTotal = $record->total;
        }
    }
    


}