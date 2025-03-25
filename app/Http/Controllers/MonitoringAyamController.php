<?php

namespace App\Http\Controllers;

use App\Models\Ayam;
use App\Models\MonitoringAyam;
use App\Models\Kandang;
use App\Services\MonitoringGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringAyamController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search'); // Pencarian berdasarkan tanggal
        $id_ayam = $request->input('id_ayam'); // Filter ayam berdasarkan periode
        $id_kandang = $request->input('id_kandang'); // Filter berdasarkan kandang
    
        $query = MonitoringAyam::query()
            ->join('ayam', 'monitoring_ayam.ayam_id', '=', 'ayam.id_ayam')
            ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
    
        // Jika user tidak memilih ayam, gunakan ayam terbaru
        if (!$id_ayam) {
            $latestAyam = Ayam::orderBy('id_ayam', 'desc')->first();
            if ($latestAyam) {
                $query->where('monitoring_ayam.ayam_id', $latestAyam->id_ayam);
            }
        } else {
            $query->where('monitoring_ayam.ayam_id', $id_ayam);
        }
    
        // Filter berdasarkan kandang jika dipilih
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
    
        // Filter pencarian berdasarkan tanggal
        if ($search) {
            $query->where('tanggal', 'like', '%' . $search . '%');
        }
    
        // Urutkan berdasarkan ayam terbaru lalu tanggal naik
        $query->orderBy('monitoring_ayam.ayam_id', 'desc')
              ->orderBy('monitoring_ayam.tanggal', 'asc');
    
        $data = $query->paginate(50);
    
        return view('pages.inventory.monitoring.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => Ayam::all(),
            'id_ayam' => $id_ayam, // Pastikan ini tetap tersimpan di filter
            'kandangs' => \App\Models\Kandang::all(),
        ]);
    }
    
    public function create()
    {
        // $sekats = Sekat::all();
        $ayams = \App\Models\Ayam::all(); // Mengambil semua data dari tabel unit

        $kandangs = Kandang::all();
        // $sekats = Sekat::all(); // Mengambil semua sekat untuk dropdown
        return view('pages.inventory.monitoring.add', [
            // 'sekats' => $sekats,
            'kandangs' => $kandangs,
            'ayams' => $ayams
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'ayam_id' => 'required|exists:ayam,id_ayam',

            'skat_1_bw' => 'required|integer|min:0',
            'skat_2_bw' => 'required|integer|min:0',
            'skat_3_bw' => 'required|integer|min:0',
            'skat_4_bw' => 'required|integer|min:0',
            'tanggal_monitoring' => 'required|date',
        ]);
    
        DB::beginTransaction();
        try {
            // Cari record monitoring berdasarkan tanggal
            $monitoring = MonitoringAyam::where('ayam_id', $request->ayam_id)->first();
            $monitoring = MonitoringAyam::where('tanggal', $request->tanggal_monitoring)->first();
            
            if (!$monitoring) {
                throw new \Exception('Data monitoring untuk tanggal tersebut tidak ditemukan!');
            }
    
            // Gunakan MonitoringGeneratorService
            $monitoringService = new MonitoringGeneratorService();
            
            // Update measurement menggunakan service
            $monitoring = $monitoringService->updateMeasurement(
                $monitoring->id,
                [
                    'skat_1_bw' => $request->input('skat_1_bw'),
                    'skat_2_bw' => $request->input('skat_2_bw'),
                    'skat_3_bw' => $request->input('skat_3_bw'),
                    'skat_4_bw' => $request->input('skat_4_bw'),
                ]
            );
    
            DB::commit();
            return redirect()->route('inventory.monitoring.index')
                ->with('success', 'Monitoring berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inventory.monitoring.index')
                ->with('error', $e->getMessage());
        }
    }
    public function print(Request $request) 
{
    $query = MonitoringAyam::query();
    
    // Gunakan join yang sama seperti di method index
    $query->join('ayam', 'monitoring_ayam.ayam_id', '=', 'ayam.id_ayam')
          ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
    
    // Sesuaikan where clause dengan nama kolom yang benar
    if ($request->id_ayam) {
        $query->where('ayam_id', $request->id_ayam);
    }
    
    if ($request->id_kandang) {
        $query->where('ayam.kandang_id', $request->id_kandang);
    }
    
    $data = $query->get();
    
    return view('pages.inventory.monitoring.print', [
        'data' => $data,
        'periode' => $request->id_ayam ? Ayam::find($request->id_ayam)->periode : 'Semua Periode',
        'kandang' => $request->id_kandang ? Kandang::find($request->id_kandang)->nama_kandang : 'Semua Kandang'
    ]);
}   
}
