<?php

namespace App\Http\Controllers;
use App\Models\Pakan;

use Illuminate\Http\Request;

class PakanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Pakan::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama_pakan', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
    
        return view('pages.lainnya.pakan', [
            'data' => $data,
            'search' => $search,
        ]);
    }
   

    // Menyimpan data kandang baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pakan' => 'required|string|max:255',
            'harga' => 'required|integer|min:1',

        ]);

        Pakan::create($request->all());

        return redirect()->route('lainnya.pakan.index')->with('success', 'pakan berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit kandang
    public function edit(Pakan $pakan)
    {
        return view('pakan.edit', compact('pakan'));
    }

    // Mengupdate data kandang
    public function update(Request $request, Pakan $pakan)
    {
        $request->validate([
            'nama_pakan' => 'required|string|max:255',
            'harga' => 'required|integer|min:1',
        ]);

        $pakan->update($request->all());

        return redirect()->route('lainnya.pakan.index')->with('success', 'Pakan berhasil diperbarui.');
    }

    // Menghapus kandang
    public function destroy(Pakan $pakan)
    {
        $pakan->delete();
        return redirect()->route('lainnya.pakan.index')->with('success', 'Pakan berhasil dihapus.');
    }
}
