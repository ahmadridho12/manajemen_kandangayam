<?php

namespace App\Http\Controllers;
use App\Models\MonitoringPakan;
use App\Models\Ayam;
use App\Models\Kandang;
use App\Models\PakanTransfer;

use App\Services\MonitoringPakanGeneratorService;
use Illuminate\Http\Request;

class MonitoringPakanController extends Controller
{
    //
    protected $monitoringPakanService;
    
    public function __construct(MonitoringPakanGeneratorService $monitoringPakanService)
    {
        $this->monitoringPakanService = $monitoringPakanService;
    }
    
    public function updatePakanMasuk(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required|string',
            'masuk' => 'required|numeric',
            'berat_zak' => 'required|numeric'
        ]);
        
        $this->monitoringPakanService->updatePakanMasuk(
            $id,
            $request->jenis,
            $request->masuk,
            $request->berat_zak
        );
        
        return redirect()->back()->with('success', 'Data pakan masuk berhasil diperbarui');
    }
    
    public function updatePakanKeluar(Request $request, $id)
    {
        $request->validate([
            'keluar' => 'required|numeric'
        ]);
        
        $this->monitoringPakanService->updatePakanKeluar(
            $id,
            $request->keluar
        );
        
        return redirect()->back()->with('success', 'Data pakan keluar berhasil diperbarui');
    }

    public function transferPakan(Request $request)
    {
        $request->validate([
            'from_monitoring_id' => 'required|exists:monitoring_pakan,id_monitoring_pakan',
            'to_monitoring_id' => 'required|exists:monitoring_pakan,id_monitoring_pakan',
            'jumlah' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $this->monitoringPakanService->transferPakan(
                $request->from_monitoring_id,
                $request->to_monitoring_id,
                $request->jumlah,
                $request->keterangan
            );

            return redirect()->back()->with('success', 'Transfer pakan berhasil dilakukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function getAvailableTransferTargets($monitoring_id)
    {
        // Ambil semua monitoring pakan yang aktif selain monitoring_id ini
        $monitoring = MonitoringPakan::findOrFail($monitoring_id);
        
        $available_targets = MonitoringPakan::with('ayam')
            ->where('id_monitoring_pakan', '!=', $monitoring_id)
            ->whereHas('ayam', function($query) {
                $query->where('status', 'active');
            })
            ->whereDate('tanggal', now())
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id_monitoring_pakan,
                    'kandang' => $item->ayam->kandang->nama_kandang,
                    'periode' => $item->ayam->periode
                ];
            });

        return response()->json($available_targets);
    }

    public function index(Request $request)
    {
        $search = $request->input('search'); // Jika ada pencarian
        $id_ayam = $request->input('id_ayam'); // Input dari dropdown filter
        $id_kandang = $request->input('id_kandang'); // Filter kandang


        $query = MonitoringPakan::query();

        $query->join('ayam', 'monitoring_pakan.ayam_id', '=', 'ayam.id_ayam') // Join ke tabel ayam
      ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang'); // Join ke tabel kandang


        if ($search) {
            $query->where('day', 'like', '%' . $search . '%');
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

        return view('pages.pakan.monitoringpakan.index', [
            'data' => $data,
            'search' => $search,
            // 'sekats' => Sekat::all(),
            'ayams' => Ayam::all(),
            'id_ayam' => $id_ayam, // Dikirim ke Blade agar filter tetap terpilih
            'kandangs' => \App\Models\Kandang::all(), // Ambil semua data kandang

        ]);
    }

    public function show($id)
    {
        $monitoring = MonitoringPakan::with(['ayam', 'details', 'transfersFrom', 'transfersTo'])
            ->findOrFail($id);

        return view('monitoring_pakan.show', compact('monitoring'));
    }

    public function getMonitoringByAyam($ayam_id)
    {
        $monitoring = MonitoringPakan::with(['details'])
            ->where('ayam_id', $ayam_id)
            ->orderBy('tanggal')
            ->get();

        return response()->json($monitoring);
    }

    public function getTransferHistory($monitoring_id)
    {
        $transfers = PakanTransfer::with(['monitoringPakanFrom.ayam', 'monitoringPakanTo.ayam'])
            ->where('monitoring_pakan_from_id', $monitoring_id)
            ->orWhere('monitoring_pakan_to_id', $monitoring_id)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function($transfer) use ($monitoring_id) {
                return [
                    'tanggal' => $transfer->tanggal,
                    'tipe' => $transfer->monitoring_pakan_from_id == $monitoring_id ? 'Keluar' : 'Masuk',
                    'jumlah' => $transfer->jumlah,
                    'kandang_lawan' => $transfer->monitoring_pakan_from_id == $monitoring_id 
                        ? $transfer->monitoringPakanTo->ayam->kandang->nama_kandang
                        : $transfer->monitoringPakanFrom->ayam->kandang->nama_kandang,
                    'keterangan' => $transfer->keterangan
                ];
            });

        return response()->json($transfers);
    }

    public function print(Request $request) 
    {
        $query = MonitoringPakan::query();
        
        // Perbaikan join dengan nama tabel yang benar 'monitoring_pakan'
        $query->join('ayam', 'monitoring_pakan.ayam_id', '=', 'ayam.id_ayam')
              ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
        
        // Sesuaikan where clause
        if ($request->id_ayam) {
            $query->where('monitoring_pakan.ayam_id', $request->id_ayam);
        }
        
        if ($request->id_kandang) {
            $query->where('ayam.kandang_id', $request->id_kandang);
        }
        
        $data = $query->get();
        
        return view('pages.pakan.monitoringpakan.print', [
            'data' => $data,
            'periode' => $request->id_ayam ? Ayam::find($request->id_ayam)->periode : 'Semua Periode',
            'kandang' => $request->id_kandang ? Kandang::find($request->id_kandang)->nama_kandang : 'Semua Kandang'
        ]);
    }
}
