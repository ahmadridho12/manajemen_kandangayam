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
        // Konversi tanggal panen ke string dengan format Y-m-d
        $tanggalPanenStr = Carbon::parse($panen->tanggal_panen)->toDateString();
        
        // Ambil record populasi berdasarkan ayam dan tanggal yang konsisten
        $populasi = Populasi::where('populasi', $panen->ayam_id)
            ->whereDate('tanggal', $tanggalPanenStr)
            ->first();
        
        if ($populasi) {
            // Ambil semua data panen untuk tanggal tersebut dengan format yang sama
            $allPanens = Panen::where('ayam_id', $panen->ayam_id)
                ->whereDate('tanggal_panen', $tanggalPanenStr)
                ->get();
            
            // Hitung total quantity panen untuk tanggal itu
            $totalPanenQuantity = $allPanens->sum('quantity');
            
            // Update kolom panen (misalnya, simpan id panen terakhir)
            $populasi->panen = $panen->id_panen; 
            
            // Update kolom qty_panen dengan total quantity panen
            $populasi->qty_panen = $totalPanenQuantity;
            
            // Update total sisa populasi: qty_now - (qty_mati + total panen)
            $populasi->total = $populasi->qty_now - ($populasi->qty_mati + $totalPanenQuantity);
            $populasi->save();
    
            // Update record pada hari-hari berikutnya dengan format tanggal yang konsisten
            $this->updateSubsequentDays($panen->ayam_id, $tanggalPanenStr);
        }
    }
    
    private function updateSubsequentDays($ayamId, $startDate) {
        $subsequentRecords = Populasi::where('populasi', $ayamId)
            ->whereDate('tanggal', '>', $startDate)
            ->orderBy('tanggal')
            ->get();
    
        $previousTotal = Populasi::where('populasi', $ayamId)
            ->whereDate('tanggal', $startDate)
            ->value('total');
    
        foreach ($subsequentRecords as $record) {
            $record->qty_now = $previousTotal;
            $record->total = $previousTotal - ($record->qty_mati + $record->qty_panen);
            $record->save();
            
            $previousTotal = $record->total;
        }
    }
    
}