<?php

namespace App\Http\Controllers;
use App\Models\AyamMati;
use App\Models\Ayam;
use App\Models\Populasi;
use App\Services\PopulasiGeneratorService;


use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AyammatiController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = AyamMati::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('tanggal_mati', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $ayams = Ayam::all(); // Ambil semua data Kandang

        return view('pages.sistem.keluar.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,

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
    public function destroy($id_ayam_mati)
    {

     $m = AyamMati::findOrFail($id_ayam_mati);
     $m->delete();
        return redirect()->route('sistem.keluar.index')->with('success', 'Ayam Mati deleted successfully.');
    }
}
