<?php

namespace App\Http\Controllers;
use App\Models\HargaAyam;

use Illuminate\Http\Request;

class HargaAyamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = HargaAyam::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('harga', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
    
        return view('pages.lainnya.hargaayam', [
            'data' => $data,
            'search' => $search,
        ]);
    }
   

    // Menyimpan data kandang baru
    public function store(Request $request)
    {
        $request->validate([
        'min_berat' => 'required|numeric',
        'max_berat' => 'required|numeric',
        'harga' => 'required|integer|min:1',
        ]);

        HargaAyam::create($request->all());

        return redirect()->route('lainnya.hargaayam.index')->with('success', 'Harga Ayam berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit kandang
    public function edit(HargaAyam $hargaayam)
    {
        return view('hargaayam.edit', compact('hargaayam'));
    }

    // Mengupdate data kandang
    public function update(Request $request, HargaAyam $hargaayam)
    {
        $request->validate([
        'min_berat' => 'required|numeric',
        'max_berat' => 'required|numeric',
        'harga' => 'required|integer|min:1',         
        ]);

        $hargaayam->update($request->all());

        return redirect()->route('lainnya.hargaayam.index')->with('success', 'Harga Ayam berhasil diperbarui.');
    }

    // Menghapus kandang
    public function destroy(HargaAyam $hargaayam)
    {
        $hargaayam->delete();
        return redirect()->route('lainnya.hargaayam.index')->with('success', 'Harga Ayam berhasil dihapus.');
    }
}
