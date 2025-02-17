<?php

namespace App\Http\Controllers;

use App\Models\Barangg;
use App\Models\DetailBarangKeluar;
use App\Models\Detailbarangmasuk;
use App\Models\Detailstok;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Laporan;
use App\Models\Jenis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
{
    try {
        $search = $request->input('search', '');
        $since = $request->input('since', null);
        $until = $request->input('until', null);
        $id_jenis = $request->input('id_jenis', null);

        $jenisOptions = Jenis::all()->pluck('nama', 'id');
        
        $data = Laporan::ambilDataLaporan($search, $since, $until, $id_jenis);

        return view('pages.laporan.rekap', compact('data', 'search', 'since', 'until', 'id_jenis', 'jenisOptions'));
    } catch (\Exception $e) {
        Log::error('Error in LaporanController@index: ' . $e->getMessage());
        return back()->with('error', 'An error occurred while generating the report.');
    }
}
    public function debugLaporan($search, $since, $until, $id_jenis)
    {
        // Debug transaksi masuk
        $debugMasuk = DB::table('detail_masuk as dm')
            ->join('barangg as b', 'dm.id_barang', '=', 'b.id_barang')
            ->select([
                'dm.id_detailmasuk',
                'b.kode_barang',
                'b.deskripsi',
                'dm.harga_setelah_ppn',
                'dm.jumlah',
                'dm.created_at'
            ])
            ->when($since && $until, function($query) use ($since, $until) {
                return $query->whereBetween('dm.created_at', [$since, $until]);
            })
            ->when($search, function($query) use ($search) {
                return $query->where('b.deskripsi', 'like', "%{$search}%");
            })
            ->when($id_jenis, function($query) use ($id_jenis) {
                return $query->where('b.id_jenis', $id_jenis);
            })
            ->get();

        // Debug transaksi keluar
        $debugKeluar = DB::table('detail_barang_keluar as dbk')
            ->join('barangg as b', 'dbk.id_barang', '=', 'b.id_barang')
            ->select([
                'dbk.id_detailstok',
                'b.kode_barang',
                'b.deskripsi',
                'dbk.harga',
                'dbk.jumlah',
                'dbk.created_at'
            ])
            ->when($since && $until, function($query) use ($since, $until) {
                return $query->whereBetween('dbk.created_at', [$since, $until]);
            })
            ->when($search, function($query) use ($search) {
                return $query->where('b.deskripsi', 'like', "%{$search}%");
            })
            ->when($id_jenis, function($query) use ($id_jenis) {
                return $query->where('b.id_jenis', $id_jenis);
            })
            ->get();

        // Debug saldo per barang
        $debugSaldo = DB::table('barangg as b')
            ->leftJoin('detail_masuk as dm', 'b.id_barang', '=', 'dm.id_barang')
            ->leftJoin('detail_barang_keluar as dbk', function($join) {
                $join->on('b.id_barang', '=', 'dbk.id_barang')
                    ->on('dm.id_detailmasuk', '=', 'dbk.id_detailstok');
            })
            ->select([
                'b.kode_barang',
                'b.deskripsi',
                DB::raw('COUNT(DISTINCT dm.id_detailmasuk) as total_transaksi_masuk'),
                DB::raw('COALESCE(SUM(dm.jumlah), 0) as total_qty_masuk'),
                DB::raw('COUNT(DISTINCT dbk.id) as total_transaksi_keluar'),
                DB::raw('COALESCE(SUM(dbk.jumlah), 0) as total_qty_keluar')
            ])
            ->when($search, function($query) use ($search) {
                return $query->where('b.deskripsi', 'like', "%{$search}%");
            })
            ->when($id_jenis, function($query) use ($id_jenis) {
                return $query->where('b.id_jenis', $id_jenis);
            })
            ->groupBy('b.kode_barang', 'b.deskripsi')
            ->get();

        $debugInfo = [
            'filters' => [
                'search' => $search,
                'since' => $since,
                'until' => $until,
                'id_jenis' => $id_jenis
            ],
            'transaksi_masuk' => $debugMasuk,
            'transaksi_keluar' => $debugKeluar,
            'saldo_per_barang' => $debugSaldo,
            'queries' => DB::getQueryLog() // Pastikan DB::enableQueryLog() sudah dipanggil
        ];

        // Return sebagai JSON untuk memudahkan debugging
        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }

    public function generateLaporanStok(Request $request)
    {
        DB::enableQueryLog(); // Enable query log

        $id_barang = $request->input('id_barang', 1);
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Debug qty keluar
        $qtyKeluar = DB::table('detail_barang_keluar')
            ->select([
                'id_barang',
                'id_detailstok',
                'harga',
                DB::raw('SUM(jumlah) as total_qty_keluar'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            ])
            ->where('id_barang', $id_barang)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('id_barang', 'id_detailstok', 'harga')
            ->get();

        $debugInfo = [
            'parameters' => [
                'id_barang' => $id_barang,
                'bulan' => $bulan,
                'tahun' => $tahun
            ],
            'qty_keluar' => $qtyKeluar,
            'queries' => DB::getQueryLog()
        ];

        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }

    public function print(Request $request)
{
    try {
        $search = $request->input('search', '');
        $since = $request->input('since', null);
        $until = $request->input('until', null);
        $id_jenis = $request->input('id_jenis', null);

        $jenisOptions = Jenis::all()->pluck('nama', 'id');

        // Ambil data laporan dengan parameter yang sama
        $data = Laporan::ambilDataLaporan($search, $since, $until, $id_jenis);

        // Format bulan dari tanggal yang dimasukkan
        $bulan = '';
        if ($since) {
            $bulan = Carbon::createFromFormat('Y-m-d', $since)->translatedFormat('F Y'); // Format bulan dan tahun
        } elseif ($until) {
            $bulan = Carbon::createFromFormat('Y-m-d', $until)->translatedFormat('F Y');
        } else {
            $bulan = Carbon::now()->translatedFormat('F Y'); // Jika tidak ada filter, gunakan bulan sekarang
        }

        // Render tampilan untuk laporan cetak
        return view('pages.laporan.print', compact('data', 'search', 'since', 'until', 'id_jenis', 'jenisOptions', 'bulan'));
    } catch (\Exception $e) {
        Log::error('Error in LaporanController@printLaporan: ' . $e->getMessage());
        return back()->with('error', 'An error occurred while generating the print report.');
    }
}
}
