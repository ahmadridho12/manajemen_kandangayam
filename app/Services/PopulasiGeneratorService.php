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