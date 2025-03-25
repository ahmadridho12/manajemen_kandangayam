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
        $dataPanen = null;          // Inisialisasi default
        $populasiData = null;       // Inisialisasi default
        $estimasiPembelian = null;  // Inisialisasi default
        $penjualan = null;          // Inisialisasi default (untuk B. PENJUALAN)
    
        if ($request->filled('id_ayam')) {
            // Filter ayam
            $query->where('id_ayam', $request->id_ayam);
            
            // Ambil data panen & populasi
            $dataPanen = $this->indexPerformanceService->getDataPanen($request->id_ayam);
            $populasiData = $this->indexPerformanceService->getPopulasiData($request->id_ayam);
            
            // Cek success
            try {
                if ($dataPanen['success']) {
                    $data = $dataPanen['data']['ringkasan'];
                    $ringkasan = [
                        'komponen' => [
                            'daya_hidup'   => $data['daya_hidup'],
                            'bobot_badan'  => $data['bobot_panen_rata_rata'],
                            'umur'         => $data['umur_rata_rata'],
                            'fcr'          => $data['fcr']
                        ],
                        'data' => $dataPanen['data'] // data IP
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Error calculating IP: ' . $e->getMessage());
            }
    
            // Ambil estimasi pembelian (DOC, pakan, obat)
            $estimasiPembelian = $this->indexPerformanceService->getEstimasiPembelian($request->id_ayam);
    
            // ----------------------------------------------
            //  B. PENJUALAN
            // ----------------------------------------------
            // Dapatkan data penjualan (total_bb, average_harga, total_panen, total_pembelian)
            $totalBB        = $dataPanen['data']['total']['total_bb']        ?? 0; // Contoh: 23617 (kg)
            $avgHarga       = $dataPanen['data']['total']['average_harga']   ?? 0; // Contoh: 22471
            $totalPanen     = $dataPanen['data']['total']['total_panen']     ?? 0; // Contoh: 530706490 (rupiah)
            $totalPembelian = $estimasiPembelian['total_pembelian']          ?? 0; // Contoh: 428284000 (rupiah)
    
            // Hitung bonus
            $bonusFcr       = $totalBB * 250;    // Contoh: 5.904.335
            $bonusKematian  = $totalBB * 100;    // Contoh: 2.361.734
    
            // Total penjualan (penjualan daging)
            $totalPenjualan = $totalBB * $avgHarga; // Karena total_panen adalah hasil penjualan daging
    
            // Laba = total penjualan + bonus - total pembelian
            $labaBersih     = $totalPenjualan + $bonusFcr + $bonusKematian - $totalPembelian;
    
            // Susun array penjualan
            $penjualan = [
                'penjualan_daging' => [
                    'qty'          => $totalBB,       // 23617 kg
                    'harga_satuan' => $avgHarga,      // 22471
                    'jumlah'       => $totalPanen     // 530706490
                ],
                'total_penjualan'  => $totalPenjualan,
                'bonus_fcr'        => $bonusFcr,
                'bonus_kematian'   => $bonusKematian,
                'laba'             => $labaBersih
            ];
        }
        
        // Filter kandang
        if ($request->filled('id_kandang')) {
            $query->where('kandang_id', $request->id_kandang);
        }
        $query->orderBy('id_ayam', 'desc');
        $ayams = Ayam::orderBy('id_ayam', 'desc')->get();

        // Ambil data paginated
        $data = $query->paginate(10);
    
        // Return view dengan semua variabel
        return view('pages.performa.ip.index', compact(
            'data', 
            'search', 
            'ayams',
            'kandangs', 
            'dataPanen', 
            'populasiData',
            'ringkasan',
            'estimasiPembelian',
            'penjualan'
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
// Tambahkan metode show yang mengarah ke metode print
public function show(Request $request)
{
    return $this->print($request);
}

public function print(Request $request) 
{
    // Ambil parameter filter
    $idAyam = $request->input('id_ayam');
    $idKandang = $request->input('id_kandang');

    // Query data dengan filter
    $query = Ayam::with(['kandang', 'doc'])
        ->when($idAyam, function ($q) use ($idAyam) {
            return $q->where('id_ayam', $idAyam);
        })
        ->when($idKandang, function ($q) use ($idKandang) {
            return $q->where('kandang_id', $idKandang);
        });

    $ayam = $query->first();

    if (!$ayam) {
        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }

    // Gunakan IndexPerformanceService untuk mengambil data
    $dataPanen = $this->indexPerformanceService->getDataPanen($ayam->id_ayam);
    $populasi = $this->indexPerformanceService->getPopulasiData($ayam->id_ayam);
    $populasiData = $populasi['status'] ? $populasi['data'] : [];
    $estimasiPembelian = $this->indexPerformanceService->getEstimasiPembelian($ayam->id_ayam);

    // Struktur ringkasan sesuai dengan yang ada di index method
    $ringkasan = $dataPanen['success'] ? [
        'komponen' => [
            'daya_hidup'   => $dataPanen['data']['ringkasan']['daya_hidup'],
            'bobot_badan'  => $dataPanen['data']['ringkasan']['bobot_panen_rata_rata'],
            'umur'         => $dataPanen['data']['ringkasan']['umur_rata_rata'],
            'fcr'          => $dataPanen['data']['ringkasan']['fcr']
        ],
        'data' => $dataPanen['data']
    ] : null;

    // Hitung penjualan sama seperti di index method
    $totalBB        = $dataPanen['data']['total']['total_bb']        ?? 0;
    $avgHarga       = $dataPanen['data']['total']['average_harga']   ?? 0;
    $totalPanen     = $dataPanen['data']['total']['total_panen']     ?? 0;
    $totalPembelian = $estimasiPembelian['total_pembelian']          ?? 0;

    $bonusFcr       = $totalBB * 250;
    $bonusKematian  = $totalBB * 100;
    $totalPenjualan = $totalBB * $avgHarga;
    $labaBersih     = $totalPenjualan + $bonusFcr + $bonusKematian - $totalPembelian;

    $penjualan = [
        'penjualan_daging' => [
            'qty'          => $totalBB,
            'harga_satuan' => $avgHarga,
            'jumlah'       => $totalPanen
        ],
        'total_penjualan'  => $totalPenjualan,
        'bonus_fcr'        => $bonusFcr,
        'bonus_kematian'   => $bonusKematian,
        'laba'             => $labaBersih
    ];

    $populasiData = [
        'populasi_awal'    => $populasiData['populasi_awal'] ?? 0,
        'ayam_mati'        => $populasiData['ayam_mati'] ?? 0,
        'persentase_mati'  => $populasiData['persentase_mati'] ?? 0,
        'ayam_sisa'        => $populasiData['ayam_sisa'] ?? 0,
        'ayam_panen'       => $populasiData['ayam_panen'] ?? 0
    ];
    
    $data = Ayam::where('id_ayam', $idAyam)->get();

    // Tampilkan view cetak
    return view('pages.performa.ip.print', compact(
        'ayam',
        'data',
        'ringkasan', 
        'populasiData', 
        'dataPanen', 
        'estimasiPembelian', 
        'penjualan'
    ));
}

}
