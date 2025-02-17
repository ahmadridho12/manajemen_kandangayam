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
        $search = $request->input('search'); // Jika ada pencarian
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang

        $query = MonitoringAyam::query();
        $query->join('ayam', 'monitoring_ayam.ayam_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
      ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang'); // Join ke tabel kandang



        if ($search) {
            $query->where('tanggal', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
          // Add ayam filter if selected
          if ($id_ayam) {
            $query->where('ayam_id', $id_ayam);
        }

         // Filter kandang
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(50); // 10 item per halaman        return view('ayam.index', compact('ayam'));

        return view('pages.inventory.monitoring.index', [
            'data' => $data,
            'search' => $search,
            // 'sekats' => Sekat::all(),
            'ayams' => Ayam::all(),

            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Ambil semua data kandang            'ayams' => Ayam::all(),
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
}
