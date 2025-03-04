<?php
namespace App\Http\Controllers;
use App\Models\HargaDoc;

use Illuminate\Http\Request;

class HargadocController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = HargaDoc::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('tahun', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
    
        return view('pages.lainnya.doc', [
            'data' => $data,
            'search' => $search,
        ]);
    }
    

      // Menyimpan data kandang baru
      public function store(Request $request)
      {
          $request->validate([
            'tahun' => 'required|integer|min:1900|max:' . date('Y'),
            'harga' => 'required|integer|min:1',
  
          ]);
  
          HargaDoc::create($request->all());
  
          return redirect()->route('lainnya.doc.index')->with('success', 'Harga DOC berhasil ditambahkan.');
      }
      public function edit(HargaDoc $doc)
    {
        return view('doc.edit', compact('doc'));
    }
    
    // Mengupdate data kandang
    public function update(Request $request, HargaDoc $doc)
    {
        $request->validate([
        'tahun' => 'required|integer|min:1900|max:' . date('Y'),
        'harga' => 'required|integer|min:1',
         
        ]);

        $doc->update($request->all());

        return redirect()->route('lainnya.doc.index')->with('success', 'Harga DOC berhasil diperbarui.');
    }

    // Menghapus kandang
    public function destroy(HargaDoc $doc)
    {
        $doc->delete();
        return redirect()->route('lainnya.doc.index')->with('success', 'Harga DOC berhasil dihapus.');
    }
}
