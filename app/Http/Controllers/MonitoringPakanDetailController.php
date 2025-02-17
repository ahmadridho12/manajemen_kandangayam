<?php

namespace App\Http\Controllers;
use App\Models\MonitoringPakanDetail;
use App\Models\Ayam;
use App\Models\Kandang;
use App\Models\Pakan;

use Illuminate\Http\Request;

class MonitoringPakanDetailController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search'); // Jika ada pencarian
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang


        $query = MonitoringPakanDetail::query();

        $query->join('ayam', 'monitoring_pakan_detail.ayam_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
      ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang'); // Join ke tabel kandang


        if ($search) {
            $query->where('masuk', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
        
          // Add ayam filter if selected
          if ($id_ayam) {
            $query->where('ayam_id', $id_ayam);
        }

         // Filter kandang
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(50); // 10 item per halaman        return view('ayam.index', compact('ayam'));

        return view('pages.pakan.stokpakan.index', [
            'data' => $data,
            'search' => $search,
            // 'sekats' => Sekat::all(),
            'pakans' => Pakan::all(),
            'ayams' => Ayam::all(),
            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Ambil semua data kandang

        ]);
    }

}
