<?php

namespace App\Http\Controllers;
use App\Models\Ayam;
use App\Models\Operasional;
use App\Models\Kandang;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperasionalController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Operasional::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama_potongan', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $kandangs = Kandang::all(); // Ambil semua data Kandang
        $ayams = Ayam::all(); // Ambil semua data Kandang

        return view('pages.gaji.operasional.index', [
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
        'nama_potongan' => 'required|string|max:50',
        'jumlah' => 'required|numeric|min:0',
        'tanggal' => 'required|date',
    ]);

    try {
        // Simpan data ke database
        DB::table('potongan_operasional')->insert([
            'kandang_id' => $validatedData['kandang_id'],
            'ayam_id' => $validatedData['ayam_id'],
            'nama_potongan' => $validatedData['nama_potongan'],
            'jumlah' => $validatedData['jumlah'],
            'tanggal' => $validatedData['tanggal'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect ke halaman gaji.operasional.index
        return redirect()->route('gaji.operasional.index')->with('success', 'Data berhasil disimpan!');
    } catch (\Exception $e) {
        // Redirect kembali jika ada error
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    public function edit($id)
    {
        $op = Operasional::findOrFail($id);  // Mengambil data berdasarkan id dari model Operasional
        $kandangs = Kandang::all(); // Mendapatkan data kandang
        $ayams = Ayam::all(); // Mendapatkan data ayam

        return view('operasional.edit', compact('op', 'kandangs', 'ayams'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kandang_id' => 'required',
            'ayam_id' => 'required',
            'nama_potongan' => 'required|string|max:255',
            'jumlah' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);
    
        $operasional = Operasional::findOrFail($id);
        $operasional->update([
            'kandang_id' => $request->kandang_id,
            'ayam_id' => $request->ayam_id,
            'nama_potongan' => $request->nama_potongan,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ]);
    
        return redirect()->route('gaji.operasional.index')->with('success', 'Data berhasil diperbarui!');
    }
    public function destroy($id)
    {
        $operasional = Operasional::findOrFail($id);
        $operasional->delete();
    
        return redirect()->route('gaji.operasional.index')->with('success', 'Data berhasil dihapus!');
    }
        

}
