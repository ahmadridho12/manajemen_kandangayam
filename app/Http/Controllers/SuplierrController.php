<?php

namespace App\Http\Controllers;
use App\Models\Suplierr;
use Illuminate\Http\Request;

class SuplierrController extends Controller
{
      //
      public function index(Request $request)
      {
          $search = $request->input('search');
      
          // Membuat query dasar
          $query = Suplierr::query();
      
          // Jika ada parameter pencarian, tambahkan kondisi WHERE
          if ($search) {
              $query->where('nama_suplier', 'like', '%' . $search . '%');
              // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
          }
      
          // Menggunakan paginate untuk mendapatkan instance Paginator
          $data = $query->paginate(10); // 10 item per halaman
      
          return view('pages.lainnya.suplierr', [
              'data' => $data,
              'search' => $search,
          ]);
      }
      public function store(Request $request)
  {
      // Validate input
      $request->validate([
          'nama_suplier' => 'required|string|max:255',
          'alamat' => 'required|string|max:255',
        //   'npwp' => 'required|string|max:255',
        //   'note' => 'required|string|max:255',
      ]);
  
      // Create a new category
      Suplierr::create([
          'nama_suplier' => $request->nama_suplier,
          'alamat' => $request->alamat,
          'npwp' => $request->npwp,
          'note' => $request->note,
      ]);
  
      // Redirect or show a success message
      return redirect()->route('lainnya.suplierr.index')->with('success', 'Bagian berhasil ditambahkan!');
  }
  
  public function update(Request $request, $id_suplier)
  {
      // Validate input
      $request->validate([
          'nama_suplier' => 'required|string|max:255',
          'alamat' => 'required|string|max:255',
          'npwp' => 'required|string|max:255',
          'note' => 'required|string|max:255',
      ]);
  
      // Find the category
      $suplierr = Suplierr::findOrFail($id_suplier);
      $suplierr->update([
          'nama_suplier' => $request->nama_suplier,
          'alamat' => $request->alamat,
          'npwp' => $request->npwp,
          'note' => $request->note,
      ]);
  
      // Redirect or show a success message
      return redirect()->route('lainnya.suplierr.index')->with('success', 'Suplier berhasil diperbarui!');
  }
  
  public function destroy($id_suplier)
  {
      // Find the category
      $suplierr = Suplierr::findOrFail($id_suplier);
      $suplierr->delete();
  
      // Redirect or show a success message
      return redirect()->route('lainnya.suplierr.index')->with('success', 'Suplier berhasil dihapus!');
  }
  
}
