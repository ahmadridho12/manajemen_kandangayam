<?php

namespace App\Http\Controllers;

use App\Models\Kandang;
use Illuminate\Http\Request;

class KandangController extends Controller
{
    // Menampilkan daftar kandang
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Kandang::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama_kandang', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
    
        return view('pages.lainnya.kandang', [
            'data' => $data,
            'search' => $search,
        ]);
    }
    // public function index()
    // {
    //     $kandang = Kandang::all();
    //     return view('kandang.index', compact('kandang'));
    // }

    // Menampilkan form untuk menambah kandang
   

    // Menyimpan data kandang baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kandang' => 'required|string|max:255',
            // 'tanggal_mulai' => 'required|date',
            // 'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        Kandang::create($request->all());

        return redirect()->route('lainnya.kandang.index')->with('success', 'Kandang berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit kandang
    public function edit(Kandang $kandang)
    {
        return view('kandang.edit', compact('kandang'));
    }

    // Mengupdate data kandang
    public function update(Request $request, Kandang $kandang)
    {
        $request->validate([
            'nama_kandang' => 'required|string|max:255',
            // 'tanggal_mulai' => 'required|date',
            // 'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $kandang->update($request->all());

        return redirect()->route('lainnya.kandang.index')->with('success', 'Kandang berhasil diperbarui.');
    }

    // Menghapus kandang
    public function destroy(Kandang $kandang)
    {
        $kandang->delete();
        return redirect()->route('lainnya.kandang.index')->with('success', 'Kandang berhasil dihapus.');
    }
}