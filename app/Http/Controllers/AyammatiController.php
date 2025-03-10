<?php

namespace App\Http\Controllers;
use App\Models\AyamMati;
use App\Models\Ayam;
use App\Models\Populasi;
use App\Services\PopulasiGeneratorService;

use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AyammatiController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang
    
        // Membuat query dasar
        $query = AyamMati::query();
        $query->join('ayam', 'ayam_mati.ayam_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
        ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('tanggal_mati', 'like', '%' . $search . '%');
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
        $query->orderBy('ayam_mati.tanggal_mati', 'desc');

        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(30); // 10 item per halaman
        $ayams = Ayam::all(); // Ambil semua data Kandang

        return view('pages.sistem.keluar.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => Ayam::orderBy('id_ayam', 'desc')->get(), // Urutkan ayam berdasarkan yang terbaru
            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Am

        ]);
    }
    public function store(Request $request) {
        $request->validate([
            'ayam_id' => 'required|exists:ayam,id_ayam',
            'tanggal_mati' => 'required|date',
            'quantity_mati' => 'required|integer|min:0',
            'alasan' => 'required|string|max:255',
        ]);
    
        DB::beginTransaction();
        try {
            // Create the AyamMati record
            $ayamMati = AyamMati::create($request->all());
    
            // Update populasi menggunakan service
            $populasiService = new PopulasiGeneratorService();
            $populasiService->updatePopulasiByAyamMati($ayamMati);
    
            DB::commit();
            return redirect()->route('sistem.keluar.index')
                ->with('success', 'Data ayam mati berhasil ditambahkan dan populasi diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(AyamMati $ayam_mati)
    {
        return view('ayam_mati.edit', compact('ayam_mati', 'ayam'));
    }

    private $populasiService;

    public function __construct(PopulasiGeneratorService $populasiService)
    {
        $this->populasiService = $populasiService;
    }

    public function update(Request $request, $id_ayam_mati)
    {
        $request->validate([
            'ayam_id' => 'required|exists:ayam,id_ayam',
            'tanggal_mati' => 'required|date',
            'quantity_mati' => 'required|integer|min:0',
            'alasan' => 'required|string|max:255',
        ]);

        $m = AyamMati::findOrFail($id_ayam_mati);

        $m->update([
            'ayam_id' => $request->ayam_id,
            'tanggal_mati' => $request->tanggal_mati,
            'quantity_mati' => $request->quantity_mati,
            'alasan' => $request->alasan,
        ]);

        // Update data di tabel populasi
        $this->populasiService->updatePopulasiByAyamMati($m);

        return redirect()->route('sistem.keluar.index')->with('success', 'Ayam Mati berhasil diperbarui!');
    }

    public function destroy($id_ayam_mati): RedirectResponse
{
    DB::beginTransaction();
    try {
        // Ambil data ayam mati yang akan dihapus
        $ayamMati = AyamMati::findOrFail($id_ayam_mati);

        // Panggil service untuk rollback perubahan populasi akibat pencatatan ayam mati
        $this->populasiService->rollbackPopulasiByAyamMati($ayamMati);

        // Hapus record ayam mati
        $ayamMati->delete();

        DB::commit();
        return redirect()->route('sistem.keluar.index')
            ->with('success', 'Data ayam mati berhasil dihapus dan populasi diperbarui');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()
            ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
}

//     public function destroy($id_ayam_mati)
// {
//     $m = AyamMati::findOrFail($id_ayam_mati);
//     $ayamId = $m->ayam_id;
//     $tanggalMati = Carbon::parse($m->tanggal_mati);
    
//     $m->delete();
    
//     // Regenerate ulang populasi dari tanggal tersebut dan setelahnya
//     $populasiService = new PopulasiGeneratorService();
//     $populasiService->updatePopulasiAfterDeletion($ayamId, $tanggalMati->toDateString());
    
//     return redirect()->route('sistem.keluar.index')
//         ->with('success', 'Ayam Mati deleted successfully and population updated.');
// }
}

