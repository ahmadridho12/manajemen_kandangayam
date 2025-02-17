<?php

namespace App\Http\Controllers;
use App\Models\Bagian;

use Illuminate\Http\Request;

class BagianController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Bagian::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama_bagian', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
    
        return view('pages.lainnya.bagian', [
            'data' => $data,
            'search' => $search,
        ]);
    }
    public function store(Request $request)
{
    // Validate input
    $request->validate([
        'nama_bagian' => 'required|string|max:255',
    ]);

    // Create a new category
    Bagian::create([
        'nama_bagian' => $request->nama_bagian,
    ]);

    // Redirect or show a success message
    return redirect()->route('lainnya.bagian.index')->with('success', 'Bagian berhasil ditambahkan!');
}

public function update(Request $request, $id_bagian)
{
    // Validate input
    $request->validate([
        'nama_bagian' => 'required|string|max:255',
    ]);

    // Find the category
    $bagian = Bagian::findOrFail($id_bagian);
    $bagian->update([
        'nama_bagian' => $request->nama_bagian,
    ]);

    // Redirect or show a success message
    return redirect()->route('lainnya.bagian.index')->with('success', 'Bagian berhasil diperbarui!');
}

public function destroy($id_bagian)
{
    // Find the category
    $bagian = Bagian::findOrFail($id_bagian);
    $bagian->delete();

    // Redirect or show a success message
    return redirect()->route('lainnya.bagian.index')->with('success', 'Bagian berhasil dihapus!');
}

}
