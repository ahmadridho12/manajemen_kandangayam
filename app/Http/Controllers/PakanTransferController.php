<?php

namespace App\Http\Controllers;
use App\Models\PakanTransfer;

use App\Models\Pakan;
use App\Models\Ayam;
use App\Models\Kandang;
use App\Services\MonitoringPakanGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class PakanTransferController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang

        $query = PakanTransfer::query();
        $query->join('ayam', 'pakan_transfers.ayam_asal_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
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

        return view('pages.pakan.transferpakan.index', [
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
        $kandangs = \App\Models\Kandang::all(); // Mengambil semua data dari tabel unit

        // Menampilkan form untuk membuat surat izin baru
        return view('pages.pakan.transferpakan.add', [
            'ayams' => $ayams,
            'pakans' => $pakans,
            'kandangs' => $kandangs,

        ]); 
    }

    public function store(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
        'kandang_asal_id' => 'required|exists:kandang,id_kandang',
        'kandang_tujuan_id' => 'required|exists:kandang,id_kandang|different:kandang_asal_id',
        'ayam_asal_id' => 'required|exists:ayam,id_ayam',
        'ayam_tujuan_id' => 'required|exists:ayam,id_ayam|different:ayam_asal_id',
        'pakan_id' => 'required|exists:pakan,id_pakan',
        'qty' => 'required|integer|min:1',
        'berat_zak' => 'required|integer|min:1',
        'keterangan' => 'nullable|string'
    ]);

    try {
        $monitoringService = new MonitoringPakanGeneratorService();
        $transfer = $monitoringService->transfer($request->all());
        
        return redirect()->route('pakan.transferpakan.index')
                        ->with('success', 'Transfer pakan berhasil dilakukan');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Gagal melakukan transfer: ' . $e->getMessage())
            ->withInput();
    }
}
}
