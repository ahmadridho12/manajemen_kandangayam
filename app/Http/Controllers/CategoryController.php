<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan
use App\Models\Jenis;

class CategoryController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Membuat query dasar
    $query = Jenis::query();

    // Jika ada parameter pencarian, tambahkan kondisi WHERE
    if ($search) {
        $query->where(function ($query) use ($search) {
            $query->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('kode', 'like', '%' . $search . '%'); // Menambahkan pencarian untuk kolom 'kode'
        });
    }

    // Menggunakan paginate untuk mendapatkan instance Paginator
    $data = $query->paginate(10); // 10 item per halaman

    return view('pages.inventory.category.index', [
        'data' => $data,
        'search' => $search,
    ]);
}
public function store(Request $request)
{
    // Validate input
    $request->validate([
        'kode' => 'required|string|max:255',
        'nama' => 'required|string|max:255',
    ]);

    // Create a new category
    Jenis::create([
        'kode' => $request->kode,
        'nama' => $request->nama,
    ]);

    // Redirect or show a success message
    return redirect()->route('inventory.category.index')->with('success', 'Kelompok berhasil ditambahkan!');
}

public function update(Request $request, $id)
{
    // Validate input
    $request->validate([
        'kode' => 'required|string|max:255',
        'nama' => 'required|string|max:255',
    ]);

    // Find the category
    $jenis = Jenis::findOrFail($id);
    $jenis->update([
        'kode' => $request->kode,
        'nama' => $request->nama,
    ]);

    // Redirect or show a success message
    return redirect()->route('inventory.category.index')->with('success', 'Kelompok berhasil diperbarui!');
}

public function destroy($id)
{
    // Find the category
    $jenis = Jenis::findOrFail($id);
    $jenis->delete();

    // Redirect or show a success message
    return redirect()->route('inventory.category.index')->with('success', 'Kelompok berhasil dihapus!');
}

}
