<?php

namespace App\Http\Controllers;

use App\Models\Ayam;
// use App\Models\Sekat;
use App\Models\Kandang;
use App\Models\AyamMati;
use App\Models\Populasi;
use App\Models\Panen;
use App\Services\PopulasiGeneratorService;
use App\Services\MonitoringGeneratorService;
use App\Services\MonitoringPakanGeneratorService;
// use App\Service\PopulasiGeneratorService;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AyamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search'); // Jika ada pencarian

        $query = Ayam::query();

        if ($search) {
            $query->where('periode', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman        return view('ayam.index', compact('ayam'));

        return view('pages.sistem.masuk.index', [
            'data' => $data,
            'search' => $search,
            // 'sekats' => Sekat::all(),
            'kandangs' => Kandang::all(),
        ]);
    }

    public function create()
    {
        // $sekats = Sekat::all();
        $kandangs = Kandang::all();
        // $sekats = Sekat::all(); // Mengambil semua sekat untuk dropdown
        return view('pages.sistem.masuk.add', [
            // 'sekats' => $sekats,
            'kandangs' => $kandangs,
        ]);
    }

    

    public function store(Request $request)
{
        $request->validate([
            'periode' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_masuk',
            'qty_ayam' => 'required|integer|min:0',
            'kandang_id' => 'required|integer',
        ]);
    
        DB::beginTransaction();
        try {
            // Simpan data ayam
            $ayam = Ayam::create([
                'periode' => $request->periode,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_selesai' => $request->tanggal_selesai,
                'qty_ayam' => $request->qty_ayam,
                'status' => 'active',
                'kandang_id' => $request->kandang_id,
            ]);
    
            // Gunakan PopulasiGeneratorService untuk generate populasi
            $populasiService = new PopulasiGeneratorService();
            $populasiService->generateFromAyam($ayam->id_ayam);
            
            // Generate data monitoring
            $monitoringService = new MonitoringGeneratorService();
            $monitoringService->generateFromAyam($ayam->id_ayam);
            
            // Generate data monitoring
         $monitoringpakanService = new MonitoringPakanGeneratorService();
         $monitoringpakanService->generateFromAyam($ayam->id_ayam);
            DB::commit();
            return redirect()->route('sistem.masuk.index')
                ->with('success', 'Data ayam dan populasi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }


    // Command untuk update populasi harian (bisa dijalankan via scheduler)
    public function updateDailyPopulation()
    {
        // Ambil semua ayam yang masih active
        $activeAyams = Ayam::where('status', 'active')->get();

        foreach ($activeAyams as $ayam) {
            // Ambil record populasi terakhir untuk ayam ini
            $lastPopulasi = Populasi::where('populasi', $ayam->id_ayam)
                ->orderBy('day', 'desc')
                ->first();

            if ($lastPopulasi) {
                // Hitung total kematian dan panen hari ini
                $totalMati = AyamMati::where('ayam_id', $ayam->id_ayam)
                    ->whereDate('tanggal', Carbon::today())
                    ->sum('jumlah');

                $totalPanen = Panen::where('ayam_id', $ayam->id_ayam)
                    ->whereDate('tanggal', Carbon::today())
                    ->sum('jumlah');

                // Buat record populasi baru untuk hari ini
                $newPopulasi = Populasi::create([
                    'tanggal' => Carbon::today(),
                    'populasi' => $ayam->id_ayam,
                    'mati' => $totalMati,
                    'panen' => $totalPanen,
                    'total' => $lastPopulasi->total - ($totalMati + $totalPanen),
                    'day' => $lastPopulasi->day + 1
                ]);

                // Jika sudah mencapai day 35, update status ayam jadi inactive
                if ($newPopulasi->day >= 35) {
                    $ayam->update(['status' => 'inactive']);
                }
            }
        }
    }

    public function edit(Ayam $ayam)
    {
        // $sekats = Sekat::all(); // Mengambil semua sekat untuk dropdown
        $kandangs = Kandang::all();
        return view('ayam.edit', compact('ayam', 'kandang'));
    }

    public function update(Request $request, $id_ayam)
    {
        $request->validate([
            // 'sekat_id' => 'required|exists:sekat,id_sekat',
            'periode' => 'required|string|max:255',
            'tanggal_selesai' => 'required|date',
            'tanggal_masuk' => 'required|date',
            'qty_ayam' => 'required|integer|min:1',
            'status' => 'required|string',
            'kandang.*.kandang_id' => 'required|exists:kandang,id_kandang',
        ]);
        $ayam = Ayam::find($id_ayam);
        if (!$ayam) {
            return redirect()->route('sistem.masuk.index')->with('error', 'Ayam tidak ditemukan.');
        }

        //simpan
        $ayam->update([
            'periode' => $request->periode,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tanggal_masuk' => $request->tanggal_masuk,
            'qty_ayam' => $request->qty_ayam,
            'status' => $request->status,
            'qty_ayam' => $request->qty_ayam,
            'kandang_id' => $request->kandang_id,
        ]);

            // Regenerate populasi data
        $populasiService = new PopulasiGeneratorService();
        $populasiService->generateFromAyam($id_ayam);
        return redirect()->route('sistem.masuk.index')->with('success', 'Ayam berhasil diperbarui.');
    }

    public function destroy($id_ayam)
{

    $ayam = Ayam::findOrFail($id_ayam);

    $ayam->delete();

    return redirect()->route('sistem.masuk.index')->with('success', 'Ayam berhasil dihapus.');
}
} 
