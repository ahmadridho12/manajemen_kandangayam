<?php

namespace App\Http\Controllers;
use App\Models\PakanKeluar;
use App\Models\Pakan;
use App\Models\Ayam;
use App\Models\Kandang;
use App\Services\MonitoringPakanGeneratorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class PakanKeluarController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang

        $query = PakanKeluar::query();
        $query->join('ayam', 'pakan_keluar.ayam_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
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
        $query->orderBy('pakan_keluar.tanggal', 'desc');

        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $ayams = Ayam::all(); // Ambil semua data Kandang
        $pakans = Pakan::all(); // Ambil semua data Kandang
        // $kandangs = Kandang::all(); // Ambil semua data Kandang

        return view('pages.pakan.pakankeluar.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => Ayam::orderBy('id_ayam', 'desc')->get(), // Urutkan ayam berdasarkan yang terbaru
            'pakans' => $pakans,
            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Ambil semua data kandang  

        ]);
    }

    public function create(): View
    {
        $ayams = \App\Models\Ayam::orderBy('id_ayam', 'desc')->get(); // Urutkan ayam dari terbaru
        $pakans = \App\Models\Pakan::all(); // Mengambil semua data dari tabel pakan
    
        // Mengambil total stok dari kolom "masuk" pada tabel monitoring_pakan_detail,
        // berdasarkan kombinasi ayam_id dan pakan_id.
        $stokData = DB::table('monitoring_pakan_detail')
            ->select('ayam_id', 'pakan_id', DB::raw('SUM(masuk) as total_stok'))
            ->groupBy('ayam_id', 'pakan_id')
            ->get();
    
        Log::info('Stok Data Raw:', $stokData->toArray());
    
        // Ubah collection menjadi nested array: [ayam_id => [pakan_id => total_stok]]
        $stokDataArray = [];
        foreach ($stokData as $item) {
            $stokDataArray[$item->ayam_id][$item->pakan_id] = $item->total_stok;
        }
    
        return view('pages.pakan.pakankeluar.add', [
            'ayams'    => $ayams,
            'pakans'   => $pakans,
            'stokData' => $stokDataArray,
        ]);
    }
    
    
//     public function getStokPakan($ayam_id, $pakan_id)
// {
//     Log::info("getStokPakan called with ayam_id: {$ayam_id}, pakan_id: {$pakan_id}");

//     // Ambil total qty (masuk) dari monitoring_pakan_detail
//     $stokTersedia = DB::table('monitoring_pakan_detail')
//         ->where('ayam_id', $ayam_id)
//         ->where('pakan_id', $pakan_id)
//         ->sum('masuk'); // Menjumlahkan qty masuk untuk pakan yang dipilih

//     $stokTersedia = $stokTersedia ?? 0; // Jika null, set ke 0
//     Log::info("Stok tersedia: {$stokTersedia}");

//     return response()->json(['stok' => $stokTersedia]);
// }


    
public function store(Request $request): RedirectResponse 
{
    $request->validate([
        'ayam_id'    => 'required|exists:ayam,id_ayam',
        'pakan_id'   => 'required|exists:pakan,id_pakan',
        'tanggal'    => 'required|date',
        'qty'        => 'required|integer|min:0',
        'berat_zak'  => 'required|integer|min:0',
    ]);

    // Ambil nilai permintaan qty
    $requestedQty = $request->input('qty');

    // Cari stok yang tersedia dari MonitoringPakanDetail untuk kombinasi ayam_id & pakan_id
    $availableStock = DB::table('monitoring_pakan_detail')
        ->where('ayam_id', $request->input('ayam_id'))
        ->where('pakan_id', $request->input('pakan_id'))
        ->value('masuk') ?? 0;

    // Jika permintaan lebih besar daripada stok yang tersedia, batalkan
    if ($requestedQty > $availableStock) {
        return redirect()->back()
            ->with('error', 'Stok pakan tidak mencukupi. Stok tersedia: ' . $availableStock)
            ->withInput();
    }

    DB::beginTransaction();
    try {
        $qty = $request->input('qty');
        $berat_zak = $request->input('berat_zak');
        
        // Hitung total berat
        $total_berat = $qty * $berat_zak;

        $pakan_keluar = new PakanKeluar();
        $pakan_keluar->ayam_id = $request->input('ayam_id');
        $pakan_keluar->pakan_id = $request->input('pakan_id');
        $pakan_keluar->tanggal = $request->input('tanggal');
        $pakan_keluar->qty = $qty;
        $pakan_keluar->berat_zak = $berat_zak;
        $pakan_keluar->total_berat = $total_berat;

        // Simpan PakanKeluar terlebih dahulu
        $pakan_keluar->save();

        // Panggil proses dari MonitoringPakanGeneratorService
        $monitoringpakanService = new MonitoringPakanGeneratorService();
        $monitoringpakanService->processPakanKeluar($pakan_keluar);

        DB::commit();
        return redirect()->route('pakan.pakankeluar.index')
            ->with('success', 'Data Pakan Keluar berhasil ditambahkan dan populasi diperbarui.');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}


    public function edit(PakanKeluar $pakan_keluar)
    {
        return view('pakan_keluar.edit', compact('pakan_keluar'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ayam_id' => 'required|exists:ayam,id_ayam',
            'pakan_id' => 'required|exists:pakan,id_pakan',
            'tanggal' => 'required|date',
            'qty' => 'required|integer|min:0',
            'berat_zak' => 'required|integer|min:0',
        ]);
    
        $pk = PakanKeluar::findOrFail($id);
        
        // Simpan nilai asli sebelum update
        $oldQty = $pk->qty;
        
        // Hitung total_berat
        $total_berat = $request->qty * $request->berat_zak;
    
        $pk->update([
            'ayam_id' => $request->ayam_id,
            'pakan_id' => $request->pakan_id,
            'tanggal' => $request->tanggal,
            'qty' => $request->qty,
            'berat_zak' => $request->berat_zak,
            'total_berat' => $total_berat,
        ]);
    
        // Panggil service update, kirim juga oldQty
        $monitoringService = new MonitoringPakanGeneratorService();
        $monitoringService->processPakanKeluar($pk, true, $oldQty);
    
        return redirect()->route('pakan.pakankeluar.index')
            ->with('success', 'Data Pakan Keluar berhasil diperbarui!');
    }
    

    public function destroy($id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Cari record PakanKeluar berdasarkan ID
            $pakanKeluar = PakanKeluar::findOrFail($id);
            
            // Ambil data yang diperlukan
            $totalBerat = $pakanKeluar->total_berat;
            $ayamId = $pakanKeluar->ayam_id;
            $pakanId = $pakanKeluar->pakan_id;
            $tanggal = $pakanKeluar->tanggal;
            
            // Cari monitoring sebelumnya pada tanggal yang sama
            $monitoringSebelumnya = DB::table('monitoring_pakan')
                ->where('ayam_id', $ayamId)
                ->whereDate('tanggal', '<', $tanggal)
                ->orderBy('tanggal', 'desc')
                ->first();
    
            // Ambil nilai sisa yang akan digunakan
            $sisaBaru = $monitoringSebelumnya ? $monitoringSebelumnya->sisa : $totalBerat;
            
            // Update tabel monitoring_pakan
            DB::table('monitoring_pakan')
                ->where('ayam_id', $ayamId)
                ->whereDate('tanggal', $tanggal)
                ->update([
                    'keluar' => 0,  // Reset ke 0
                    'sisa' => $sisaBaru // Gunakan sisa dari monitoring sebelumnya
                ]);
            
            // Update tabel monitoring_pakan_detail
            $monitoringDetail = DB::table('monitoring_pakan_detail')
                ->where('ayam_id', $ayamId)
                ->where('pakan_id', $pakanId)
                ->first();
                
            if ($monitoringDetail) {
                DB::table('monitoring_pakan_detail')
                    ->where('ayam_id', $ayamId)
                    ->where('pakan_id', $pakanId)
                    ->update([
                        'masuk' => DB::raw("masuk + {$pakanKeluar->qty}"),
                        'total_berat' => DB::raw("total_berat + $totalBerat")
                    ]);
            }
            
            // Hapus record pakan_keluar
            $pakanKeluar->delete();
            
            DB::commit();
            
            return redirect()->route('pakan.pakankeluar.index')
                ->with('success', 'Data Pakan Keluar berhasil dihapus dan stok pakan telah dikembalikan.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
    public function getPakan()
    {
        $pakans = Pakan::all(); // Ambil semua barang dari database
        return response()->json($pakans); // Kembalikan sebagai JSON
    }
    public function getAyam()
    {
        $ayams = Ayam::all(); // Ambil semua barang dari database
        return response()->json($ayams); // Kembalikan sebagai JSON
    }
}
