<?php

namespace App\Http\Controllers;

use App\Models\PerhitunganGaji;
use App\Models\Kandang;
use App\Models\Ayam;
use App\Models\Operasional;
use App\Models\RincianGajiAbk;
use App\Services\GajiService;
use App\Services\SalaryCalculationService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;


class PerhitunganGajiController extends Controller
{
    protected $gajiService;

    public function __construct(SalaryCalculationService $gajiService)
    {
        $this->gajiService = $gajiService;
    }

    
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Membuat query dasar
        $query = PerhitunganGaji::query();
        $ayams = Ayam::all();
        $kandangs = Kandang::all();
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('tanggal_mati', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $ayams = Ayam::all(); // Ambil semua data Kandang

        return view('pages.gaji.penggajian.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,
            'kandangs' => $kandangs,

        ]);
    }

    public function create(): View
    {
        $kandangs = Kandang::all();
        $ayams = Ayam::all();
        return view('pages.gaji.penggajian.add', compact('kandangs', 'ayams'));
    }

    public function store(Request $request)
{
    try {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id_kandang',
            'ayam_id' => 'required|exists:ayam,id_ayam',
            'hasil_pemeliharaan' => 'required|numeric', // Validasi untuk hasil_pemeliharaan
            'bonus_per_orang' => 'required|numeric', // Validasi untuk bonus_per_orang
            'keterangan' => 'nullable|string',
        ]);

        // Ambil hasil_pemeliharaan dari input

        $hasil_pemeliharaan = $request->input('hasil_pemeliharaan'); // Ambil dari input
        $keterangan = $request->input('keterangan'); // Anda perlu menentukan nilai ini jika ada
        $bonus_per_orang = $request->input('bonus_per_orang'); // Anda perlu menentukan nilai ini jika ada

        $perhitunganGaji = $this->gajiService->calculateSalary(
            $request->ayam_id,
            $request->kandang_id,
            $hasil_pemeliharaan, // Pastikan ini diisi dengan nilai yang benar
            $bonus_per_orang,
            $keterangan
            // $keterangan,
        );

        return redirect()
    ->route('gaji.penggajian.index', $perhitunganGaji['perhitungan_gaji']->id_perhitungan)
    ->with('success', 'Perhitungan gaji berhasil disimpan');

    } catch (\Exception $e) {
        return back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

public function show($id_perhitungan): View
{
    $perhitunganGaji = PerhitunganGaji::with(['rincianGaji.abk', 'rincianGaji.pinjaman', 'kandang', 'ayam'])
        ->findOrFail($id_perhitungan);
    
    // Ambil data operasional berdasarkan periode dan kandang yang sama
    $operasional = Operasional::where('ayam_id', $perhitunganGaji->ayam_id)
                              ->where('kandang_id', $perhitunganGaji->kandang_id)
                              ->get();
    
    return view('pages.gaji.penggajian.show', compact('perhitunganGaji', 'operasional'));
}
public function print($id_perhitungan)
{
    $perhitunganGaji = PerhitunganGaji::with([
        'rincianGaji.abk', 
        'rincianGaji.pinjaman', 
        'kandang', 
        'ayam',
        'operasional'
    ])->findOrFail($id_perhitungan);
    // Ambil data operasional berdasarkan periode dan kandang yang sama
    $operasional = Operasional::where('ayam_id', $perhitunganGaji->ayam_id)
                              ->where('kandang_id', $perhitunganGaji->kandang_id)
                              ->get();
    
    return view('pages.gaji.penggajian.print', compact('perhitunganGaji', 'operasional'));
}
public function printSlip($id_rincian)
{
    $rincian = RincianGajiAbk::with([
        'abk', 
        'pinjaman', 
        'perhitunganGaji.kandang', 
        'perhitunganGaji.ayam'
    ])->findOrFail($id_rincian);
    
    return view('pages.gaji.penggajian.print-slip', compact('rincian'));
}
}
