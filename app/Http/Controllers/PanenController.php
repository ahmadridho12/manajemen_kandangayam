<?php

namespace App\Http\Controllers;
use App\Models\Panen;
use App\Models\Ayam;
use App\Models\HargaAyam;
use App\Models\Populasi;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Services\PopulasiGeneratorService;



use Illuminate\Support\Carbon;

class PanenController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang
    
        // Membuat query dasar
        $query = Panen::query();
        $query->join('ayam', 'panen.ayam_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
      ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang'); 
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('tanggal_panen', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
        if ($id_ayam) {
            $query->where('ayam_id', $id_ayam);
        }

         // Filter kandang
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
        $query->orderBy('panen.tanggal_panen', 'desc');

        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $ayams = Ayam::all(); // Ambil semua data Kandang

        return view('pages.sistem.panen.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,
            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Ambil semua data kandang  

        ]);
    }


    public function create(): View
    {
        $ayams = \App\Models\Ayam::all(); // Mengambil semua data dari tabel unit

        // Menampilkan form untuk membuat surat izin baru
        return view('pages.sistem.panen.add', [
            'ayams' => $ayams,
        ]); 
    }


    
    public function store(Request $request): RedirectResponse 
{
    $request->validate([
        'ayam_id' => 'required|exists:ayam,id_ayam',
        'tanggal_panen' => 'required|date',
        'quantity' => 'required|integer|min:0',
        'atas_nama' => 'required|string|max:255',
        'no_panen' => 'required|string|max:255',
        'berat_total' => 'required|numeric|min:0',
        'foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);
    
    DB::beginTransaction();
    try {
        $panen = new Panen();
        $panen->ayam_id = $request->ayam_id;
        $panen->tanggal_panen = $request->tanggal_panen;
        $panen->quantity = $request->quantity;
        $panen->atas_nama = $request->atas_nama;
        $panen->no_panen = $request->no_panen;
        $panen->berat_total = $request->berat_total;

        
        // Hitung Rata Berat dan bulatkan ke 2 desimal
        $rata_berat = round($request->berat_total / $request->quantity, 2);
        $panen->rata_berat = $rata_berat;


        // 🔍 Ambil Harga Otomatis dari Tabel Harga Ayam
        $harga = HargaAyam::where('min_berat', '<=', $rata_berat)
                          ->where('max_berat', '>=', $rata_berat)
                          ->first();

        if ($harga) {
            $panen->harga_id = $harga->id_harga;
            $panen->total_panen = $harga->harga * $request->berat_total;
        } else {
            throw new \Exception('Harga tidak ditemukan untuk berat rata-rata ini');
        }

        // 🔥 Upload Foto
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('photos', 'public');
            $panen->foto = $foto;
        }

        $panen->save();
          // Update populasi menggunakan service
          $populasiService = new PopulasiGeneratorService();
          $populasiService->updatePopulasiByPanen($panen);
        DB::commit();
        return redirect()->route('sistem.panen.index')->with('success', 'Panen berhasil ditambahkan');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}


    public function show($id_panen): View
    {
        // Mengambil permission dengan relasi unit
        $panen = Panen::with('ayam')->findOrFail($id_panen);
        // Mengambil permission berdasarkan id_permission
        $panen = Panen::findOrFail($id_panen);
        return view('pages.sistem.panen.show', compact('panen'));
    }

   
    public function edit($id_panen): View
{
    // Mengambil semua data dari tabel unit
    $panen = Panen::with('ayam')->findOrFail($id_panen);
    $ayams = Ayam::all();
    // dd($ayams); // Debugging untuk melihat data Ayam

    // Memperbaiki compact dengan menutup tanda kutip dengan benar
    return view('pages.sistem.panen.edit', compact('panen', 'ayams'));
}


public function update(Request $request, $id_panen): RedirectResponse
{
    $request->validate([
        'ayam_id' => 'required|exists:ayam,id_ayam',
        'tanggal_panen' => 'required|date',
        'quantity' => 'required|integer|min:0',
        'atas_nama' => 'required|string|max:255',
        'no_panen' => 'required|string|max:255',
        'berat_total' => 'required|numeric|min:0',
        'foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    DB::beginTransaction();
    try {
        $panen = Panen::findOrFail($id_panen);
        $panen->ayam_id = $request->ayam_id;
        $panen->tanggal_panen = $request->tanggal_panen;
        $panen->quantity = $request->quantity;
        $panen->atas_nama = $request->atas_nama;
        $panen->no_panen = $request->no_panen;
        $panen->berat_total = $request->berat_total;

        // ✅ Hitung Rata Berat Lagi
        $rata_berat = $request->berat_total / $request->quantity;
        $panen->rata_berat = $rata_berat;

        // 🔍 Ambil Harga Otomatis dari Tabel Harga Ayam
        $harga = HargaAyam::where('min_berat', '<=', $rata_berat)
                          ->where('max_berat', '>=', $rata_berat)
                          ->first();

        if ($harga) {
            $panen->harga_id = $harga->id_harga; // Foreign Key Harga
            $panen->total_panen = $harga->harga * $request->berat_total;
        } else {
            throw new \Exception('Harga tidak ditemukan untuk berat rata-rata ini');
        }

        // 🔥 Upload Foto Baru (Kalau Ada)
        if ($request->hasFile('foto')) {
            // Hapus Foto Lama
            if ($panen->foto) {
                Storage::disk('public')->delete($panen->foto);
            }
            $foto = $request->file('foto')->store('photos', 'public');
            $panen->foto = $foto;
        }

        $panen->save();
        // Update populasi menggunakan service
        $populasiService = new PopulasiGeneratorService();
        $populasiService->updatePopulasiByPanen($panen);
        DB::commit();
        return redirect()->route('sistem.panen.index')->with('success', 'Panen berhasil diupdate');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
    }
}
public function destroy($id_panen): RedirectResponse
{
    DB::beginTransaction();
    try {
        $panen = Panen::findOrFail($id_panen);

        // Panggil service untuk rollback nilai populasi
        $populasiService = new PopulasiGeneratorService();
        $populasiService->rollbackPopulasiByPanen($panen);

        // Hapus foto jika ada
        if ($panen->foto) {
            Storage::disk('public')->delete($panen->foto);
        }

        // Hapus data panen
        $panen->delete();

        DB::commit();
        return redirect()->route('sistem.panen.index')->with('success', 'Data panen berhasil dihapus dan populasi diperbarui');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
}




}