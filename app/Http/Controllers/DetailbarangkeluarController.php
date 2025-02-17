<?php

namespace App\Http\Controllers;

use App\Models\Detaibarangkeluar;
use Illuminate\Http\Request;
use App\Models\DetailBarangKeluar;

class DetailbarangkeluarController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = DetailBarangKeluar::with(['jenis', 'barang', 'barangKeluar' => function($q) {
            $q->orderBy('created_at', 'asc'); // Mengurutkan berdasarkan created_at dari detail_masuk
        }]);    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('harga', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
        
        $query->orderBy('created_at', 'desc');

        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(50); // 10 item per halaman
    
        return view('pages.transaksi.detailbarangkeluar.index', [
            'data' => $data,
            'search' => $search,
        ]);
    }
}
