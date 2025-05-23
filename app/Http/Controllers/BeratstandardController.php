<?php

namespace App\Http\Controllers;

use App\Models\BeratStandar;
use Illuminate\Http\Request;

class BeratstandardController extends Controller
{
    // Menampilkan daftar kandang
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = BeratStandar::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('hari_ke', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(50); // 10 item per halaman
    
        return view('pages.lainnya.beratstandard', [
            'data' => $data,
            'search' => $search,
        ]);
    }
    
   

    // Menyimpan data kandang baru
    public function store(Request $request)
    {
        $request->validate([
            'hari_ke' => 'required|integer',
            'bw' => 'required|numeric',
            'dg' => 'required|numeric',
            // 'tanggal_mulai' => 'required|date',
            // 'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        BeratStandar::create($request->all());

        return redirect()->route('lainnya.beratstandard.index')->with('success', 'Berat Standard berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit kandang
    public function edit(BeratStandar $beratstandard)
{
    return view('beratstandard.edit', compact('beratstandard'));
}


    // Mengupdate data kandang
    public function update(Request $request, BeratStandar $beratstandard)
    {
        $request->validate([
        'hari_ke' => 'required|integer',
        'bw' => 'required|numeric',
        'dg' => 'required|numeric',
    ]);

        $beratstandard->update($request->all());

        return redirect()->route('lainnya.beratstandard.index')->with('success', 'Berat Standard berhasil diperbarui.');
    }

    // Menghapus kandang
    public function destroy(BeratStandar $beratstandard)
    {
        $beratstandard->delete();
        return redirect()->route('lainnya.beratstandard.index')->with('success', 'Berat Standard berhasil dihapus.');
    }
}