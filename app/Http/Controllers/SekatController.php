<?php

namespace App\Http\Controllers;

use App\Models\Sekat;
use Illuminate\Http\Request;
use App\Models\Kandang;
class SekatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Sekat::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama_sekat', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $kandangs = Kandang::all(); // Ambil semua data Kandang

        return view('pages.lainnya.sekat', [
            'data' => $data,
            'search' => $search,
            'kandangs' => $kandangs,

        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id_kandang',
            'nama_sekat' => 'required|string|max:255',
        ]);

        Sekat::create($request->all());
        return redirect()->route('lainnya.sekat.index')->with('success', 'Sekat created successfully.');
    }

    public function edit(Sekat $sekat)
    {
        return view('sekat.edit', compact('sekat'));
    }

    public function update(Request $request, Sekat $sekat)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id_kandang',
            'nama_sekat' => 'required|string|max:255',
        ]);

        $sekat->update($request->all());
        return redirect()->route('lainnya.sekat.index')->with('success', 'Sekat updated successfully.');
    }

    public function destroy(Sekat $sekat)
    {
        $sekat->delete();
        return redirect()->route('lainnya.sekat.index')->with('success', 'Sekat deleted successfully.');
    }
}