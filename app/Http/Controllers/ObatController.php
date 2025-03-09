<?php

namespace App\Http\Controllers;
use App\Models\Ayam;
use App\Models\Obat;
use App\Models\Kandang;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Obat::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama_obat', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
        $query->orderBy('obat.created_at', 'desc');

    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $kandangs = Kandang::all(); // Ambil semua data Kandang
        $ayams = Ayam::all(); // Ambil semua data Kandang

        return view('pages.pakan.obat.obatan', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,
            'kandangs' => $kandangs,

        ]);
    }
    public function store(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'kandang_id' => 'required|exists:kandang,id_kandang', // Pastikan ID kandang valid
        'ayam_id' => 'required|exists:ayam,id_ayam', // Pastikan ID ayam valid
        'nama_obat' => 'required|string|max:50',
        'total' => 'required|numeric|min:0',
    ]);

    try {
        // Simpan data ke database
        DB::table('obat')->insert([
            'kandang_id' => $validatedData['kandang_id'],
            'ayam_id' => $validatedData['ayam_id'],
            'nama_obat' => $validatedData['nama_obat'],
            'total' => $validatedData['total'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect ke halaman gaji.operasional.index
        return redirect()->route('pakan.obat.index')->with('success', 'Data berhasil disimpan!');
    } catch (\Exception $e) {
        // Redirect kembali jika ada error
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    public function edit($id)
    {
        $obt = Obat::findOrFail($id);  // Mengambil data berdasarkan id dari model Operasional
        $kandangs = Kandang::all(); // Mendapatkan data kandang
        $ayams = Ayam::all(); // Mendapatkan data ayam

        return view('obat.edit', compact('obt', 'kandangs', 'ayams'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kandang_id' => 'required',
            'ayam_id' => 'required',
            'nama_obat' => 'required|string|max:255',
            'total' => 'required|numeric',
        ]);
    
        $obat = Obat::findOrFail($id);
        $obat->update([
            'kandang_id' => $request->kandang_id,
            'ayam_id' => $request->ayam_id,
            'nama_obat' => $request->nama_obat,
            'total' => $request->total,
        ]);
    
        return redirect()->route('pakan.obat.index')->with('success', 'Data berhasil diperbarui!');
    }
    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();
    
        return redirect()->route('pakan.obat.index')->with('success', 'Data berhasil dihapus!');
    }
        

}
