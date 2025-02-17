<?php

namespace App\Http\Controllers;
use App\Models\Barangg;
use App\Models\Jenis;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Membuat query dasar dengan relasi
    $query = Barangg::with(['jenis', 'satuan']);

    // Pencarian di tabel yang berelasi
    if ($search) {
        $query->where(function($q) use ($search) {
            // Pencarian di tabel utama
            $q->where('deskripsi', 'like', '%' . $search . '%')
              ->orWhere('kode_barang', 'like', '%' . $search . '%')
              
            //   // Pencarian di tabel berelasi
            //   ->orWhereHas('jenis', function($query) use ($search) {
            //       $query->where('nama_jenis', 'like', '%' . $search . '%');
            //   })
              ->orWhereHas('satuan', function($query) use ($search) {
                  $query->where('nama_satuan', 'like', '%' . $search . '%');
              });
        });
    }

    // Menggunakan paginate untuk mendapatkan instance Paginator
    $data = $query->paginate(20);

    $satuans = Satuan::all();

    return view('pages.inventory.goods.index', [
        'data' => $data,
        'search' => $search,
        'satuans' => $satuans,
    ]);
}


    public function create()
    {
        $jenisa = Jenis::all();
        $satuans = Satuan::all();

        return view('pages.inventory.goods.add', [
           
            'jenisa' => $jenisa,
            'satuans' => $satuans,
        ]);
    }

    
    // Metode generateKodeBarang untuk menghasilkan kode barang baru
    public function generateKodeBarang($id_jenis)
    {
        // Ambil kode jenis dari tabel jenis berdasarkan id_jenis
        $jenis = Jenis::find($id_jenis);

        if (!$jenis) {
            throw new \Exception('Jenis tidak ditemukan');
        }

        // Ambil kode_barang terakhir dengan jenis ini
        $lastBarang = Barangg::where('id_jenis', $id_jenis)
            ->orderBy('kode_barang', 'desc')
            ->first();

        // Ambil kode terakhir dari barang yang sesuai dengan jenis
        $lastKode = $lastBarang ? intval(substr($lastBarang->kode_barang, -3)) : 0;

        // Generate kode barang baru dengan urutan 3 digit
        $newKode = $jenis->kode . '.' . str_pad($lastKode + 1, 3, '0', STR_PAD_LEFT);

        return $newKode;
    }

    // Metode store untuk menyimpan barang baru
    public function store(Request $request)
    {
        // Tambahkan validasi untuk memastikan deskripsi unik
        $validatedData = $request->validate([
            'deskripsi' => 'required|unique:barangg,deskripsi', // Validasi deskripsi harus unik di tabel barangg
            'id_satuan' => 'required',
            'id_jenis' => 'required',
            // Validasi lainnya
        ], [
            'deskripsi.unique' => 'Nama barang sudah ada, silakan masukkan nama yang berbeda.', // Pesan kesalahan kustom
        ]);
    
        try {
            // Generate kode_barang berdasarkan id_jenis
            $kode_barang = $this->generateKodeBarang($request->id_jenis);
    
            // Simpan barang baru
            $barang = new Barangg();
            $barang->deskripsi = $request->deskripsi;
            $barang->id_satuan = $request->id_satuan;
            $barang->id_jenis = $request->id_jenis;
            $barang->kode_barang = $kode_barang;
            $barang->save();
    
            return redirect()->route('inventory.goods.index')->with('success', 'Barang berhasil disimpan!');
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan barang: ' . $e->getMessage()], 500);
        }
    }


    //update//
    public function update(Request $request, $id_barang)
    {

        // Validate input
        $request->validate([
            'deskripsi' => 'required|string|max:255',
            'id_satuan' => 'required',
            // 'id' => 'required',
        ]);

        $barangg = Barangg::find($id_barang);
        // $satuans = Satuan::all();

        $barangg->update([
            'deskripsi' => $request->deskripsi,
            'id_satuan' => $request->id_satuan,
        ]);

        // Redirect or show a success message
        return redirect()->route('inventory.goods.index')->with('success', 'Barang berhasil diperbarui!');


    }   
    public function getSatuans()
{
    $satuans = Satuan::all();
    return response()->json(['satuans' => $satuans]);
}

public function destroy($id_barang)
{
    // Find the category
    $barangg = Barangg::findOrFail($id_barang);
    $barangg->delete();

    // Redirect or show a success message
    return redirect()->route('inventory.goods.index')->with('success', 'Barang berhasil dihapus!');
}

}


   
    
 
