<?php

namespace App\Http\Controllers;
use App\Models\Barangkeluar;
use App\Models\Permintaan;
use App\Models\Detailbarangkeluar;
use App\Models\Barangg; 
use App\Models\Jenis;

use Illuminate\Http\Request;

class BarangkeluarController extends Controller
{
    //

    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $data = Barangkeluar::with(['permintaan']) // Pastikan relasi sudah benar
            ->when($search, function($query) use ($search) {
                return $query->where('no_transaksi', 'like', '%' . $search . '%')
                            //  ->orWhereHas('bagiann', function($q) use ($search) {
                            //      $q->where('nama_bagian', 'like', '%' . $search . '%');
                            //  })
                             ->orWhereHas('permintaan', function($q) use ($search) {
                                 $q->where('no_trans', 'like', '%' . $search . '%'); // Pencarian di kolom 'nama' dari tabel 'jenis'
                             });
            })
            ->orderBy('tanggal_keluar', 'desc') // Mengurutkan berdasarkan created_at
            ->paginate(25);
            $permintaans = Permintaan::all();
            // $bagians = Bagian::all();
            // $kategoris = Kategori::all();
       
    
        return view('pages.transaksi.barangkeluar.index', [
            'data' => $data,
            'search' => $search,
            'permintaans' => $permintaans,
        ]);
    }

    public function show($id_keluar)
    {
        // Mengambil permintaan dengan id yang diberikan, beserta relasinya
        $barangkeluar = Barangkeluar::with('detailBarangKeluar',)
            ->findOrFail($id_keluar);
    
        return view('pages.transaksi.barangkeluar.show', compact('barangkeluar'));
    }

    public function print($id_keluar)
    {
        // Ambil data permintaan berdasarkan ID
        $barangkeluar = Barangkeluar::with(['detailBarangKeluar'])->findOrFail($id_keluar);
        
        // Mengambil tanggal saat ini
        $currentDate = now()->format('d-m-Y');

        return view('pages.transaksi.barangkeluar.print', compact('barangkeluar', 'currentDate'));
    }
    
}
