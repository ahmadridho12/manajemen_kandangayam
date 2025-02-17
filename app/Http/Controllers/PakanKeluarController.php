<?php

namespace App\Http\Controllers;
use App\Models\PakanKeluar;
use App\Models\Pakan;
use App\Models\Ayam;
use App\Models\Kandang;
use App\Services\MonitoringPakanGeneratorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan
use Illuminate\Http\Request;

class PakanKeluarController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang

        $query = PakanKeluar::query();
        $query->join('ayam', 'pakan_keluar.ayam_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
      ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang'); // Join ke tabel kandang

        // Membuat query dasar
        // $query = PakanMasuk::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('berat_zak', 'like', '%' . $search . '%');
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
        $data = $query->paginate(10); // 10 item per halaman
        $ayams = Ayam::all(); // Ambil semua data Kandang
        $pakans = Pakan::all(); // Ambil semua data Kandang
        // $kandangs = Kandang::all(); // Ambil semua data Kandang

        return view('pages.pakan.pakankeluar.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,
            'pakans' => $pakans,
            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Ambil semua data kandang  

        ]);
    }

    public function create(): View
    {
        $ayams = \App\Models\Ayam::all(); // Mengambil semua data dari tabel unit
        $pakans = \App\Models\Pakan::all(); // Mengambil semua data dari tabel unit

        // Menampilkan form untuk membuat surat izin baru
        return view('pages.pakan.pakankeluar.add', [
            'ayams' => $ayams,
            'pakans' => $pakans,

        ]); 
    }
    public function store(Request $request): RedirectResponse 
    {
        $request->validate([
            'ayam_id' => 'required|exists:ayam,id_ayam',
            'pakan_id' => 'required|exists:pakan,id_pakan',
            'tanggal' => 'required|date',
            'qty' => 'required|integer|min:0',
            'berat_zak' => 'required|integer|min:0',
        ]);
    
        DB::beginTransaction();
        try {
            $qty = $request->input('qty');
            $berat_zak = $request->input('berat_zak');
            
            // Hitung total_berat
            $total_berat = $qty * $berat_zak;
    
            $pakan_keluar = new PakanKeluar();
            $pakan_keluar->ayam_id = $request->input('ayam_id');
            $pakan_keluar->pakan_id = $request->input('pakan_id');
            $pakan_keluar->tanggal = $request->input('tanggal');
            $pakan_keluar->qty = $qty;
            $pakan_keluar->berat_zak = $berat_zak;
            $pakan_keluar->total_berat = $total_berat;
    
            // Simpan PakanMasuk terlebih dahulu
            $pakan_keluar->save();
    
            // Panggil proses dari MonitoringPakanGeneratorService
            $monitoringpakanService = new MonitoringPakanGeneratorService();
            $monitoringpakanService->processPakanKeluar($pakan_keluar);
    
            DB::commit();
            return redirect()->route('pakan.pakankeluar.index')
                ->with('success', 'Data Panen berhasil ditambahkan dan populasi diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(PakanKeluar $pakan_keluar)
    {
        return view('pakan_keluar.edit', compact('pakan_keluar'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ayam_id' => 'required|exists:ayam,id_ayam',
            'pakan_id' => 'required|exists:pakan,id_pakan',
            'tanggal' => 'required|date',
            'qty' => 'required|integer|min:0',
            'berat_zak' => 'required|integer|min:0',
        ]);

        $pk = PakanKeluar::findOrFail($id);
        
        // Hitung total_berat
        $total_berat = $request->qty * $request->berat_zak;

        $pk->update([
            'ayam_id' => $request->ayam_id,
            'pakan_id' => $request->pakan_id,
            'tanggal' => $request->tanggal,
            'qty' => $request->qty,
            'berat_zak' => $request->berat_zak,
            'total_berat' => $total_berat,
        ]);

        // Buat instance service langsung di method
        $monitoringService = new MonitoringPakanGeneratorService();
        $monitoringService->processPakanKeluar($pk, true);

        return redirect()->route('pakan.pakankeluar.index')
            ->with('success', 'Data Pakan Keluar berhasil diperbarui!');
    }
}
