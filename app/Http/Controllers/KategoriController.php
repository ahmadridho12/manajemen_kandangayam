<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Create the base query
        $query = Kategori::query();
    
        // If there's a search parameter, add a WHERE condition
        if ($search) {
            $query->where('nama_tipe', 'like', '%' . $search . '%');
        }
    
        // Use paginate to get the Paginator instance
        $data = $query->paginate(10); // 10 items per page
    
        return view('pages.inventory.kategori.index', [
            'data' => $data,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'nama_tipe' => 'required|string|max:255',
            'teruntuk' => 'required|string|max:255',
        ]);

        // Create a new category
        Kategori::create([
            'nama_tipe' => $request->nama_tipe,
            'teruntuk' => $request->teruntuk,
        ]);

        // Redirect or show a success message
        return redirect()->route('inventory.kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, $id_tipe)
    {
        // Validate input
        $request->validate([
            'nama_tipe' => 'required|string|max:255',
            'teruntuk' => 'required|string|max:255',
        ]);

        // Find the category
        $tipe = Kategori::findOrFail($id_tipe);
        $tipe->update([
            'nama_tipe' => $request->nama_tipe,
            'teruntuk' => $request->teruntuk,
        ]);

        // Redirect or show a success message
        return redirect()->route('inventory.kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id_tipe)
    {
        // Find the category
        $tipe = Kategori::findOrFail($id_tipe);
        $tipe->delete();

        // Redirect or show a success message
        return redirect()->route('inventory.kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
