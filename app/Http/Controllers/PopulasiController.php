<?php

namespace App\Http\Controllers;
use App\Models\Ayam;
use App\Models\Populasi;
use App\Models\Panen;
use App\Models\AyamMati;
use App\Models\Kandang;



use Illuminate\Http\Request;

class PopulasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search'); // Jika ada pencarian
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang


        $query = Populasi::query();

        $query->join('ayam', 'populasi.populasi', '=', 'ayam.id_ayam') // Join ke tabel ayam
      ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang'); // Join ke tabel kandang


        if ($search) {
            $query->where('day', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
        
          // Add ayam filter if selected
          if ($id_ayam) {
            $query->where('populasi', $id_ayam);
        }

         // Filter kandang
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(50); // 10 item per halaman        return view('ayam.index', compact('ayam'));

        return view('pages.inventory.populasi.index', [
            'data' => $data,
            'search' => $search,
            // 'sekats' => Sekat::all(),
            'ayams' => Ayam::all(),
            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Ambil semua data kandang

            'panens' => Panen::all(),
            'ayammatis' => AyamMati::all(),
        ]);
    }

    public function print(Request $request) 
{
    $query = Populasi::query();
    
    // Gunakan join yang sama seperti di method index
    $query->join('ayam', 'populasi.populasi', '=', 'ayam.id_ayam')
          ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
    
    // Sesuaikan where clause dengan nama kolom yang benar
    if ($request->id_ayam) {
        $query->where('populasi', $request->id_ayam);
    }
    
    if ($request->id_kandang) {
        $query->where('ayam.kandang_id', $request->id_kandang);
    }
    
    $data = $query->get();
    
    return view('pages.inventory.populasi.print', [
        'data' => $data,
        'periode' => $request->id_ayam ? Ayam::find($request->id_ayam)->periode : 'Semua Periode',
        'kandang' => $request->id_kandang ? Kandang::find($request->id_kandang)->nama_kandang : 'Semua Kandang'
    ]);
}   
}
