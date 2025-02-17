<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stok;
use App\Models\Jenis;
use App\Models\Barangg;
use App\Models\Barangmasuk;
use App\Models\Detailbarangmasuk;

class StokController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Membuat query dasar dan memuat relasi dengan 'jenis' dan 'barangg'
    $query = Stok::with(['jenis', 'barangg', 'detailMasuk' => function($q) {
        $q->orderBy('tgl_masuk', 'asc'); // Mengurutkan berdasarkan tgl_masuk dari detail_masuk
    }]);

    // Jika ada parameter pencarian, tambahkan kondisi WHERE
    if ($search) {
        $keywords = explode(' ', $search); // Memisahkan kata kunci
        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $searchTerm = '%' . $keyword . '%';
                // Pencarian di tabel utama
                $q->orWhere('qty', 'like', $searchTerm)
                  // Pencarian di tabel berelasi
                  ->orWhereHas('barangg', function($query) use ($searchTerm) {
                      $query->where('kode_barang', 'like', $searchTerm)
                            ->orWhere('deskripsi', 'like', $searchTerm);
                  });
            }
        });
    }

    // Menggunakan paginate untuk mendapatkan instance Paginator
    $data = $query->paginate(40); // 40 item per halaman

    // Mengambil semua barangs dan jenisa
    $barangs = Barangg::all();
    $jenisa = Jenis::all();

    // Mengisi nilai harga_fifo untuk setiap stok
    foreach ($data as $stok) {
        $stok->harga_fifo = $this->getHargaFIFO($stok);
    }

    // Mengirim data ke view
    return view('pages.inventory.stok.index', [
        'data' => $data,
        'search' => $search,
        'barangs' => $barangs,
        'jenisa' => $jenisa,
    ]);
}
    public function getHargaFIFO($stok)
{
    $totalQtyStok = $stok->qty; // Jumlah stok yang tersisa
    $hargaFIFO = 0;

    foreach ($stok->detailMasuk as $detail) {
        if ($totalQtyStok <= 0) {
            break; // Jika stok sudah habis, keluar dari loop
        }

        if ($totalQtyStok <= $detail->jumlah) {
            // Jika stok masih mencukupi di batch ini
            $hargaFIFO = $detail->harga_setelah_ppn;
            break;
        } else {
            // Kurangi stok dari batch ini dan lanjutkan ke batch berikutnya
            $totalQtyStok -= $detail->jumlah;
        }
    }

    // Jika stok tidak ditemukan atau tidak ada data, periksa apakah stok kosong
    if ($hargaFIFO == 0 && $stok->detailMasuk->isEmpty()) {
        // \Log::info('Stok tidak ditemukan untuk id_barang: ' . $stok->id_barang);
    }

    return $hargaFIFO;
}
}
