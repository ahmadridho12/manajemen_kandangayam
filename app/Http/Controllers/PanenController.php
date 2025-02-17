<?php

namespace App\Http\Controllers;
use App\Models\Panen;
use App\Models\Ayam;
use App\Models\Populasi;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan

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
    
        // Membuat query dasar
        $query = Panen::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('tanggal_panen', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $ayams = Ayam::all(); // Ambil semua data Kandang

        return view('pages.sistem.panen.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,

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
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Foto tidak wajib
        ]);
    
        DB::beginTransaction();
        try {
            $panen = new Panen();
            $panen->ayam_id = $request->input('ayam_id');
            $panen->tanggal_panen = $request->input('tanggal_panen');
            $panen->quantity = $request->input('quantity');
            $panen->atas_nama = $request->input('atas_nama');
            $panen->no_panen = $request->input('no_panen');
            $panen->berat_total = $request->input('berat_total');
    
            // Jika file foto di-upload, proses penyimpanan
            if ($request->hasFile('foto')) {
                $fotoPatch = $request->file('foto')->store('photos', 'public');
                $panen->foto = $fotoPatch;
            } else {
                $panen->foto = null;
            }
            
            $panen->save();
    
            // Update populasi menggunakan service
            $populasiService = new PopulasiGeneratorService();
            $populasiService->updatePopulasiByPanen($panen);
    
            DB::commit();
            return redirect()->route('sistem.panen.index')
                ->with('success', 'Data Panen berhasil ditambahkan dan populasi diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
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

    // public function edit(AyamMati $ayam_mati)
    // {
    //     return view('ayam_mati.edit', compact('ayam_mati', 'ayam'));
    // }

    // public function update(Request $request, $id_ayam_mati)
    // {
    //     $request->validate([
    //         'ayam_id' => 'required|exists:ayam,id_ayam',
    //         'tanggal_mati' => 'required|date',
    //         'quantity_mati' => 'required|integer|min:0',
    //         'alasan' => 'required|string|max:255',
    //     ]);

    //     $m = AyamMati::find($id_ayam_mati);

    //     $m->update([
    //         'ayam_id' => $request->ayam_id,
    //         'tanggal_mati' => $request->tanggal_mati,
    //         'quantity_mati' => $request->quantity_mati,
    //         'alasan' => $request->alasan,
    //     ]);
    //     return redirect()->route('sistem.keluar.index')->with('success', 'Ayam Mati berhasil diperbarui!');
    // }

    // public function destroy($id_ayam_mati)
    // {

    //  $m = AyamMati::findOrFail($id_ayam_mati);
    //  $m->delete();
    //     return redirect()->route('sistem.keluar.index')->with('success', 'Ayam Mati deleted successfully.');
    // }
    public function edit($id_panen): View
{
    // Mengambil semua data dari tabel unit
    $panen = Panen::with('ayam')->findOrFail($id_panen);
    $ayams = Ayam::all();
    // dd($ayams); // Debugging untuk melihat data Ayam

    // Memperbaiki compact dengan menutup tanda kutip dengan benar
    return view('pages.sistem.panen.edit', compact('panen', 'ayams'));
}


public function update(Request $request, $id_panen)
{
    // Validasi input
    $request->validate([
        'ayam_id' => 'required',
        'tanggal_panen' => 'required',
        'quantity' => 'required',
        'berat_total' => 'required',
        'atas_nama' => 'required',
        'no_panen' => 'required|string|max:255',
        'foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Foto tidak wajib
    ]);

    // Temukan permission berdasarkan ID
    // dd($request->all()); // Menampilkan semua data yang diterima

    $panen = Panen::findOrFail($id_panen);

    $panen->tanggal_panen = $request->tanggal_panen;
    $panen->quantity = $request->quantity;
    $panen->berat_total = $request->berat_total;
    $panen->atas_nama = $request->atas_nama;
    $panen->no_panen = $request->no_panen;
    
    // dd($request->all());
    //ambil unit
    $ayam = Ayam::findOrFail($request->ayam_id);
    $panen->ayam_id = $ayam->id_ayam;
    //ambil jabatan
    // dd($request->all());

    // Jika file foto di-upload, proses penyimpanan
    if ($request->hasFile('foto')) {
        $fotoPath = $request->file('foto')->store('photos', 'public');
        $panen->foto = $fotoPath;
    }

    // Simpan data ke database
    $panen->save();

    $populasiService = new PopulasiGeneratorService();
    $populasiService->generateFromAyam($ayam->id_ayam);
    
    return redirect()->route('sistem.panen.index')
    ->with('success', 'Data Panen berhasil diperbarui.');
}


}