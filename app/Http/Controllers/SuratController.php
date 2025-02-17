<?php

namespace App\Http\Controllers;
use App\Models\Surat;

use Illuminate\Http\Request;

class SuratController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = Surat::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nosurat', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
        // dd($query->toSql(), $query->getBindings()); // Cek query SQL dan parameter yang digunakan
        $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');


        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
    
        return view('pages.lainnya.surat', [
            'data' => $data,
            'search' => $search,
        ]);
    }
}
