<?php

namespace App\Http\Controllers;
use App\Models\Ayam;
use App\Models\Pinjaman;
use App\Models\Kandang;
use App\Models\Abk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PinjamanController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Pinjaman::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama_potongan', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $abks = Abk::all(); // Ambil semua data Kandang
        $kandangs = Kandang::all(); // Ambil semua data Kandang
        $ayams = Ayam::all(); // Ambil semua data Kandang

        return view('pages.gaji.pinjaman.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,
            'kandangs' => $kandangs,
            'abks' => $abks,

        ]);
    }

   
    public function store(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'abk_id' => 'required|exists:abk,id_abk', // Pastikan ID kandang valid
        'kandang_id' => 'required|exists:kandang,id_kandang', // Pastikan ID kandang valid
        'ayam_id' => 'required|exists:ayam,id_ayam', // Pastikan ID ayam valid
        'jumlah_pinjaman' => 'required|numeric|min:0',
        'tanggal_pinjam' => 'required|date',
    ]);

    try {
        // Simpan data ke database
        DB::table('pinjaman_abk')->insert([
            'abk_id' => $validatedData['abk_id'],
            'kandang_id' => $validatedData['kandang_id'],
            'ayam_id' => $validatedData['ayam_id'],
            'jumlah_pinjaman' => $validatedData['jumlah_pinjaman'],
            'tanggal_pinjam' => $validatedData['tanggal_pinjam'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect ke halaman gaji.operasional.index
        return redirect()->route('gaji.pinjaman.index')->with('success', 'Data Pinajamn berhasil disimpan!');
    } catch (\Exception $e) {
        // Redirect kembali jika ada error
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function edit($id)
    {
        $pj = Pinjaman::findOrFail($id);  // Mengambil data berdasarkan id dari model Operasional
        $abks = Abk::all(); // Mendapatkan data kandang
        $kandangs = Kandang::all(); // Mendapatkan data kandang
        $ayams = Ayam::all(); // Mendapatkan data ayam

        return view('pinjaman.edit', compact('pj', 'abks', 'kandangs', 'ayams'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'abk_id' => 'required',
            'kandang_id' => 'required',
            'ayam_id' => 'required',
            'jumlah_pinjaman' => 'required|numeric',
            'tanggal_pinjam' => 'required|date',
        ]);
    
        $pinjaman = Pinjaman::findOrFail($id);
        $pinjaman->update([
            'abk_id' => $request->abk_id,
            'kandang_id' => $request->kandang_id,
            'ayam_id' => $request->ayam_id,
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'tanggal_pinjam' => $request->tanggal_pinjam,
        ]);
    
        return redirect()->route('gaji.pinjaman.index')->with('success', 'Data berhasil diperbarui!');
    }
    public function destroy($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $pinjaman->delete();
    
        return redirect()->route('gaji.pinjaman.index')->with('success', 'Data berhasil dihapus!');
    }
}
