<?php

namespace App\Http\Controllers;

use App\Models\Detailbarangmasuk;
use Illuminate\Http\Request;

class DetailbarangmasukController extends Controller
{
    //

    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Detailbarangmasuk::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
         // Jika ada parameter pencarian, tambahkan kondisi WHERE
    if ($search) {
        $query->where('jumlah', 'like', '%' . $search . '%');

        // Pencarian berdasarkan no_transaksi dan keterangan di tabel 'barang_masuk'
        $query->orWhereHas('barangMasuk', function($q) use ($search) {
            $q->where('no_transaksi', 'like', '%' . $search . '%') // Pencarian pada kolom no_transaksi
              ->orWhere('keterangan', 'like', '%' . $search . '%'); // Pencarian pada kolom keterangan
        });

        // Pencarian berdasarkan deskripsi barang di tabel 'barangg'
        $query->orWhereHas('barang', function($q) use ($search) {
            $q->where('deskripsi', 'like', '%' . $search . '%');
        });
    }
        
        $query->orderBy('created_at', 'desc');

        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(25); // 10 item per halaman
    
        return view('pages.transaksi.detailbarangmasuk.index', [
            'data' => $data,
            'search' => $search,
        ]);
    }
}
