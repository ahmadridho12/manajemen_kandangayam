<?php

namespace App\Http\Controllers;
use App\Models\Ayam;
use App\Models\Populasi;
use App\Models\Panen;
use App\Models\AyamMati;
use App\Models\Kandang;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use Illuminate\Http\Request;

class PopulasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search'); // Pencarian berdasarkan hari
        $id_ayam = $request->input('id_ayam'); // Filter ayam berdasarkan periode
        $id_kandang = $request->input('id_kandang'); // Filter kandang
    
        $query = Populasi::query()
            ->join('ayam', 'populasi.populasi', '=', 'ayam.id_ayam')
            ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
    
        // Gunakan id_ayam terbaru hanya jika user tidak memilih filter manual
        if (!$id_ayam) {
            $latestAyam = Ayam::orderBy('id_ayam', 'desc')->first();
            if ($latestAyam) {
                $query->where('populasi.populasi', $latestAyam->id_ayam);
                $id_ayam = $latestAyam->id_ayam; // Agar filter tetap terlihat di view
            }
        } else {
            $query->where('populasi.populasi', $id_ayam);
        }
    
        // Filter pencarian berdasarkan hari
        if ($search) {
            $query->where('day', 'like', '%' . $search . '%');
        }
        if ($id_ayam) {
            $query->where('populasi', $id_ayam);
        }
        // Filter kandang
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
    
        // Urutkan berdasarkan ayam terbaru lalu hari dalam periode naik
        $query->orderBy('populasi.populasi', 'desc')
              ->orderBy('populasi.day', 'asc');
    
        $data = $query->paginate(50);
    
        return view('pages.inventory.populasi.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => Ayam::orderBy('id_ayam', 'desc')->get(), // Urutkan ayam berdasarkan yang terbaru
            'id_ayam' => $id_ayam, // Pastikan ini tetap ada di filter
            'kandangs' => \App\Models\Kandang::all(),
            'panens' => Panen::all(),
            'ayammatis' => AyamMati::all(),
        ]);
    }
    


    public function print(Request $request) 
{
    $query = Populasi::query();
    
    // Gunakan join yang sama seperti di method index
    $query->join('ayam', 'populasi.populasi', '=', 'ayam.id_ayam')
          ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
    
    // Sesuaikan where clause dengan nama kolom yang benar
    if ($request->id_ayam) {
        $query->where('populasi', $request->id_ayam);
    }
    
    if ($request->id_kandang) {
        $query->where('ayam.kandang_id', $request->id_kandang);
    }
    
    $data = $query->get();
    
    return view('pages.inventory.populasi.print', [
        'data' => $data,
        'periode' => $request->id_ayam ? Ayam::find($request->id_ayam)->periode : 'Semua Periode',
        'kandang' => $request->id_kandang ? Kandang::find($request->id_kandang)->nama_kandang : 'Semua Kandang'
    ]);
}   
public function getChartData(Request $request)
{
    try {
        // Ambil parameter filter
        $id_ayam = $request->input('id_ayam');
        $id_kandang = $request->input('id_kandang');
        
        // Log untuk debugging
        Log::info('Chart Data Request', [
            'id_ayam' => $id_ayam,
            'id_kandang' => $id_kandang
        ]);
        
        // Query untuk data dari tabel populasi
        $query = DB::table('populasi')
            ->join('ayam', 'populasi.populasi', '=', 'ayam.id_ayam');
            
        if ($id_ayam) {
            $query->where('ayam.id_ayam', $id_ayam);
        }
        
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
        
        // Ambil data berdasarkan hari (day)
        $data = $query->select(
                'populasi.day', 
                'populasi.tanggal',
                'populasi.qty_mati',
                'populasi.qty_panen',
                'populasi.total as total_populasi' 
                )
            ->orderBy('populasi.day', 'asc')
            ->get();
        
        // Jika tidak ada data
        if ($data->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada data untuk filter yang dipilih',
                'labels' => [],
                'qty_mati_series' => [],
                'qty_panen_series' => [],
                'populasi_series' => [],
                'total_mati' => 0,
                'total_panen' => 0
            ]);
        }
        
        // Siapkan data untuk chart
        $labels = [];
        $qtyMatiSeries = [];
        $qtyPanenSeries = [];
        $populasiSeries = [];
        $totalMati = 0;
        $totalPanen = 0;
        
        foreach ($data as $item) {
            $labels[] = 'Hari ' . $item->day . ' (' . Carbon::parse($item->tanggal)->format('d/m') . ')';
            $qtyMatiSeries[] = (int)$item->qty_mati;
            $qtyPanenSeries[] = (int)$item->qty_panen;
            $populasiSeries[] = (int)$item->total_populasi; // gunakan alias
            
            $totalMati += (int)$item->qty_mati;
            $totalPanen += (int)$item->qty_panen;
        }
        
        return response()->json([
            'success' => true,
            'labels' => $labels,
            'qty_mati_series' => $qtyMatiSeries,
            'qty_panen_series' => $qtyPanenSeries,
            'populasi_series' => $populasiSeries,
            'total_mati' => $totalMati,
            'total_panen' => $totalPanen
        ]);
        
    } catch (\Exception $e) {
        Log::error('Chart Data Error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

}
