<?php

namespace App\Http\Controllers;

use App\Models\PakanMasuk;
use App\Models\Pakan;
use App\Models\Ayam;
use App\Models\Kandang;
use App\Services\MonitoringPakanGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan


class PakanMasukController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang

        $query = PakanMasuk::query();
        $query->join('ayam', 'pakan_masuk.ayam_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
      ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang'); // Join ke tabel kandang

        // Membuat query dasar
        // $query = PakanMasuk::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('berat_zak', 'like', '%' . $search . '%');
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
        $query->orderBy('pakan_masuk.tanggal', 'desc');
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $ayams = Ayam::all(); // Ambil semua data Kandang
        $pakans = Pakan::all(); // Ambil semua data Kandang
        // $kandangs = Kandang::all(); // Ambil semua data Kandang

        return view('pages.pakan.pakanmasuk.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,
            'pakans' => $pakans,
            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Ambil semua data kandang  

        ]);
    }


    public function create(): View
    {
        $ayams = \App\Models\Ayam::all(); // Mengambil semua data dari tabel unit
        $pakans = \App\Models\Pakan::all(); // Mengambil semua data dari tabel unit

        // Menampilkan form untuk membuat surat izin baru
        return view('pages.pakan.pakanmasuk.add', [
            'ayams' => $ayams,
            'pakans' => $pakans,
            'total_berat' => 0, // Tambahin ini biar gak undefined
            'total_harga_pakan' => 0, // Tambahin ini sekalian
            

        ]); 
    }

    public function store(Request $request): RedirectResponse 
{
    $request->validate([
        'ayam_id' => 'required|exists:ayam,id_ayam',
        'pakan_id' => 'required|exists:pakan,id_pakan',
        'tanggal' => 'required|date',
        'masuk' => 'required|integer|min:0',
        'berat_zak' => 'required|integer|min:0',
    ]);

    DB::beginTransaction();
    try {
        $masuk = $request->input('masuk');
        $berat_zak = $request->input('berat_zak');
        
        // Hitung total_berat
        $total_berat = $masuk * $berat_zak;

        // Ambil harga dari tabel Pakan
        $harga_pakan = Pakan::where('id_pakan', $request->input('pakan_id'))->value('harga');
        
        // Hitung total_harga
        $total_harga_pakan = $request->total_berat * $harga_pakan;

        $pakan_masuk = new PakanMasuk();
        $pakan_masuk->ayam_id = $request->input('ayam_id');
        $pakan_masuk->pakan_id = $request->input('pakan_id');
        $pakan_masuk->tanggal = $request->input('tanggal');
        $pakan_masuk->masuk = $masuk;
        $pakan_masuk->berat_zak = $berat_zak;
        $pakan_masuk->total_berat = $total_berat;
        $pakan_masuk->total_harga_pakan = $total_harga_pakan;

        // Simpan PakanMasuk
        $pakan_masuk->save();

        // Panggil proses dari MonitoringPakanGeneratorService
        $monitoringpakanService = new MonitoringPakanGeneratorService();
        $monitoringpakanService->processPakanMasuk($pakan_masuk);

        DB::commit();
        return redirect()->route('pakan.pakanmasuk.index')
            ->with('success', 'Data Pakan berhasil ditambahkan dan populasi diperbarui.');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}



public function edit(PakanMasuk $pakan_masuk)
{
    $ayams = Ayam::all();
    $pakans = \App\Models\Pakan::all();
    $total_berat = 0;           
    $total_harga_pakan = 0;     
    return view('pakan_masuk.edit', compact('pakan_masuk', 'ayams', 'pakans', 'total_berat', 'total_harga_pakan'));
}


    // public function __construct(MonitoringPakanGeneratorService $monitoringPakanGeneratorService)
    // {
    //     $this->monitoringPakanGeneratorService = $monitoringPakanGeneratorService;
    // }

    public function update(Request $request, $id)
{
    $request->validate([
        'ayam_id'    => 'required|exists:ayam,id_ayam',
        'pakan_id'   => 'required|exists:pakan,id_pakan',
        'tanggal'    => 'required|date',
        'masuk'      => 'required|integer|min:0',
        'berat_zak'  => 'required|integer|min:0',
    ]);

    $pm = PakanMasuk::findOrFail($id);
    
    // Simpan nilai original sebelum update
    $oldMasuk = $pm->masuk;
    $oldBeratZak = $pm->berat_zak;
    
    // Hitung total berat baru
    $total_berat = $request->masuk * $request->berat_zak;
    
    // Ambil harga pakan dari tabel Pakan
    $harga_pakan = Pakan::where('id_pakan', $request->pakan_id)->value('harga');
    // Hitung total harga pakan baru
    $total_harga_pakan = $request->total_berat * $harga_pakan;

    // Lakukan update data pakan masuk
    $pm->update([
        'ayam_id'            => $request->ayam_id,
        'pakan_id'           => $request->pakan_id,
        'tanggal'            => $request->tanggal,
        'masuk'              => $request->masuk,
        'berat_zak'          => $request->berat_zak,
        'total_berat'        => $total_berat,
        'total_harga_pakan'  => $total_harga_pakan, // update total harga
    ]);

    // Panggil service dengan parameter update serta nilai original
    $monitoringService = new MonitoringPakanGeneratorService();
    $monitoringService->processPakanMasuk($pm, true, $oldMasuk, $oldBeratZak);

    return redirect()->route('pakan.pakanmasuk.index')
                     ->with('success', 'Data Pakan Masuk berhasil diperbarui!');
}

    public function destroy($id)
    {
        $pm = PakanMasuk::findOrFail($id);
        $pm->delete();

        return redirect()->route('pakan.pakanmasuk.index')->with('success', 'Data Panen berhasil dihapus!');
    }


}
