<?php

namespace App\Http\Controllers;

use App\Models\Ayam;
// use App\Models\Sekat;
use App\Models\Kandang;
use App\Models\AyamMati;
use App\Models\HargaDoc;
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
        $query->orderBy('created_at', 'desc');

    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman        return view('ayam.index', compact('ayam'));

        return view('pages.sistem.masuk.index', [
            'data' => $data,
            'search' => $search,
            // 'sekats' => Sekat::all(),
            'kandangs' => Kandang::all(),
            'docs' => HargaDoc::all(),
        ]);
    }

    public function create()
    {
        // $sekats = Sekat::all();
        $kandangs = Kandang::all();
        $docs = HargaDoc::all();
        // $sekats = Sekat::all(); // Mengambil semua sekat untuk dropdown
        return view('pages.sistem.masuk.add', [
            // 'sekats' => $sekats,
            'kandangs' => $kandangs,
            'docs' => $docs,
        ]);
    }

    

    public function store(Request $request) {
        $request->validate([
            'periode' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'rentang_hari' => 'required|integer|min:1', // Tambahkan validasi untuk rentang hari
            'qty_ayam' => 'required|integer|min:0',
            'kandang_id' => 'required|integer',
            'doc_id' => 'required|integer',
        ]);
        
        DB::beginTransaction();
        try {
            // Hitung tanggal selesai berdasarkan rentang hari
            $tanggal_selesai = Carbon::parse($request->tanggal_masuk)
                ->addDays($request->rentang_hari)
                ->format('Y-m-d');

            // Ambil harga DOC dari tabel harga_doc berdasarkan doc_id yang dipilih
            $harga_doc = HargaDoc::where('id_doc', $request->doc_id)->first()->harga;

            // Hitung total harga ayam
            $total_harga = $request->qty_ayam * $harga_doc;    
            // Simpan data ayam
            $ayam = Ayam::create([
                'periode' => $request->periode,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_selesai' => $tanggal_selesai,
                'rentang_hari' => $request->rentang_hari, // Simpan rentang hari
                'qty_ayam' => $request->qty_ayam,
                'doc_id' => $request->doc_id,
                'total_harga' => $total_harga,
                'status' => 'active',
                'kandang_id' => $request->kandang_id,
            ]);
            
            // Gunakan PopulasiGeneratorService untuk generate populasi
            $populasiService = new PopulasiGeneratorService();
            $populasiService->generateFromAyam($ayam->id_ayam);
            
            // Generate data monitoring
            $monitoringService = new MonitoringGeneratorService();
            $monitoringService->generateFromAyam($ayam->id_ayam);
            
            // Generate data monitoring pakan
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
        'periode' => 'required|string|max:255',
        'tanggal_masuk' => 'required|date',
        'rentang_hari' => 'required|integer|min:1',
        'qty_ayam' => 'required|integer|min:0',
        'kandang_id' => 'required|integer',
        'doc_id' => 'required|integer',
        'status' => 'required|string',
    ]);

    DB::beginTransaction();
    try {
        $ayam = Ayam::find($id_ayam);
        if (!$ayam) {
            return redirect()->route('sistem.masuk.index')->with('error', 'Ayam tidak ditemukan.');
        }

        // Hitung Tanggal Selesai
        $tanggal_selesai = Carbon::parse($request->tanggal_masuk)
            ->addDays($request->rentang_hari)
            ->format('Y-m-d');

        // Ambil Harga DOC
        $harga_doc = HargaDoc::where('id_doc', $request->doc_id)->first()->harga;
        
        // Hitung Total Harga
        $total_harga = $request->qty_ayam * $harga_doc;

        // Update Data Ayam
        $ayam->update([
            'periode' => $request->periode,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_selesai' => $tanggal_selesai,
            'rentang_hari' => $request->rentang_hari,
            'qty_ayam' => $request->qty_ayam,
            'doc_id' => $request->doc_id,
            'total_harga' => $total_harga,
            'status' => $request->status,
            'kandang_id' => $request->kandang_id,
        ]);

        // Regenerate Populasi
        $populasiService = new PopulasiGeneratorService();
        $populasiService->generateFromAyam($ayam->id_ayam);

        // Regenerate Monitoring
        $monitoringService = new MonitoringGeneratorService();
        $monitoringService->generateFromAyam($ayam->id_ayam);

        // Regenerate Monitoring Pakan
        $monitoringpakanService = new MonitoringPakanGeneratorService();
        $monitoringpakanService->generateFromAyam($ayam->id_ayam);

        DB::commit();
        return redirect()->route('sistem.masuk.index')
            ->with('success', 'Data ayam berhasil diperbarui!');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}


    public function destroy($id_ayam)
{

    $ayam = Ayam::findOrFail($id_ayam);

    $ayam->delete();

    return redirect()->route('sistem.masuk.index')->with('success', 'Ayam berhasil dihapus.');
}
} 
