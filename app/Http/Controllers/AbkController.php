<?php

namespace App\Http\Controllers;
use App\Models\Abk;

use Illuminate\Http\Request;

class AbkController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Abk::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
    
        return view('pages.lainnya.abk', [
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
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'status' => 'nullable|in:active,nonactive', // Validasi status

        ]);

        abk::create([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'status' => $request->status ?? 'active', // Default 'active'
        ]);
        return redirect()->route('lainnya.abk.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit kandang
    public function edit(Abk $abk)
    {
        return view('abk.edit', compact('abk'));
    }

    // Mengupdate data kandang
    public function update(Request $request, Abk $abk)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'jabatan' => 'required|string|max:255',
        'status' => 'nullable|in:active,nonactive', // Validasi status
    ]);

    $abk->update([
        'nama' => $request->nama,
        'jabatan' => $request->jabatan,
        'status' => $request->status ?? $abk->status, // Jika status tidak diubah, tetap gunakan yang lama
    ]);

    return redirect()->route('lainnya.abk.index')->with('success', 'Petugas berhasil diperbarui.');
}


    // Menghapus kandang
    public function destroy(Abk $abk)
    {
        $abk->delete();
        return redirect()->route('lainnya.abk.index')->with('success', 'Petugas berhasil dihapus.');
    }
}
