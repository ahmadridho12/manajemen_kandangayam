<?php

namespace App\Http\Controllers;

use App\Services\IndexPerformanceService;
use Illuminate\Http\Request;
use App\Models\Ayam;
use App\Models\Kandang;
use Illuminate\Support\Facades\Log;

class PerformaController extends Controller
{
    private $indexPerformanceService;

    public function __construct(IndexPerformanceService $indexPerformanceService)
    {
        $this->indexPerformanceService = $indexPerformanceService;
    }

    public function index(Request $request)
{
    $search = $request->get('search', '');
    $ayams = Ayam::orderBy('periode', 'desc')->get();
    $kandangs = Kandang::all();
    
    $query = Ayam::query();
    $ringkasan = null;
    $dataPanen = null; // Inisialisasi default
    $populasiData = null; // Inisialisasi default

    
    if ($request->filled('id_ayam')) {
        $query->where('id_ayam', $request->id_ayam);
        $dataPanen = $this->indexPerformanceService->getDataPanen($request->id_ayam);
        $populasiData = $this->indexPerformanceService->getPopulasiData($request->id_ayam);
        
        try {
            if ($dataPanen['success']) {
                $data = $dataPanen['data']['ringkasan'];
                $ringkasan = [
                    'komponen' => [
                        'daya_hidup' => $data['daya_hidup'],
                        'bobot_badan' => $data['bobot_panen_rata_rata'],
                        'umur' => $data['umur_rata_rata'],
                        'fcr' => $data['fcr']
                    ],
                    'data' => $dataPanen['data'] // Include semua data untuk IP
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error calculating IP: ' . $e->getMessage());
        }
    }
    
    if ($request->filled('id_kandang')) {
        $query->where('kandang_id', $request->id_kandang);
    }
    
    $data = $query->paginate(10);

    return view('pages.performa.ip.index', compact(
        'data', 
        'search', 
        'ayams', 
        'kandangs', 
        'dataPanen', 
        'populasiData',
        'ringkasan'
    ));
}

public function hitungIP(Request $request, $kandangId)
{
    try {
        $request->validate([
            'periode' => 'required|string'
        ]);

        $result = $this->indexPerformanceService->calculateIP($kandangId, $request->periode);
        
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        // Ambil data panen
        $dataPanen = $this->indexPerformanceService->getDataPanen($result['data']['ayam_id']);
        
        // Ambil data populasi berdasarkan ayam_id
        $populasiData = $this->indexPerformanceService->getPopulasiData($result['data']['ayam_id']);
        Log::info('Data populasi:', ['populasiData' => $populasiData]);

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'dataPanen' => $dataPanen,
            'populasiData' => $populasiData,
            'message' => 'Index Performance berhasil dihitung'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}
}
