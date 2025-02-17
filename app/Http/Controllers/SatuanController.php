<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan
use App\Models\Satuan;

class SatuanController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Membuat query dasar
    $query = Satuan::query();

    // Jika ada parameter pencarian, tambahkan kondisi WHERE
    if ($search) {
        $query->where('nama_satuan', 'like', '%' . $search . '%');
        // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
    }

    // Menggunakan paginate untuk mendapatkan instance Paginator
    $data = $query->paginate(10); // 10 item per halaman

    return view('pages.lainnya.satuan', [
        'data' => $data,
        'search' => $search,
    ]);
}
public function store(Request $request)
{
    // Validate input
    $request->validate([
        'nama_satuan' => 'required|string|max:255',
    ]);

    // Create a new category
    Satuan::create([
        'nama_satuan' => $request->nama_satuan,
    ]);

    // Redirect or show a success message
    return redirect()->route('lainnya.satuan.index')->with('success', 'Satuan berhasil ditambahkan!');
}

public function update(Request $request, $id_satuan)
{
    // Validate input
    $request->validate([
        'nama_satuan' => 'required|string|max:255',
    ]);

    // Find the category
    $satuan = Satuan::findOrFail($id_satuan);
    $satuan->update([
        'nama_satuan' => $request->nama_satuan,
    ]);

    // Redirect or show a success message
    return redirect()->route('lainnya.satuan.index')->with('success', 'Satuan berhasil diperbarui!');
}

public function destroy($id_satuan)
{
    // Find the category
    $satuan = Satuan::findOrFail($id_satuan);
    $satuan->delete();

    // Redirect or show a success message
    return redirect()->route('lainnya.satuan.index')->with('success', 'Satuan berhasil dihapus!');
}

}
