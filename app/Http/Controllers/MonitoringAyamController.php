<?php

namespace App\Http\Controllers;

use App\Models\Ayam;
use App\Models\MonitoringAyam;
use App\Models\Kandang;
use App\Services\MonitoringGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringAyamController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search'); // Pencarian berdasarkan tanggal
        $id_ayam = $request->input('id_ayam'); // Filter ayam berdasarkan periode
        $id_kandang = $request->input('id_kandang'); // Filter berdasarkan kandang
        $ayams = Ayam::with('kandang')->get(); // pastikan relasi kandang dimuat

        $query = MonitoringAyam::query()
            ->join('ayam', 'monitoring_ayam.ayam_id', '=', 'ayam.id_ayam')
            ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
    
        // Jika user tidak memilih ayam, gunakan ayam terbaru
        if (!$id_ayam) {
            $latestAyam = Ayam::orderBy('id_ayam', 'desc')->first();
            if ($latestAyam) {
                $query->where('monitoring_ayam.ayam_id', $latestAyam->id_ayam);
            }
        } else {
            $query->where('monitoring_ayam.ayam_id', $id_ayam);
        }
    
        // Filter berdasarkan kandang jika dipilih
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
    
        // Filter pencarian berdasarkan tanggal
        if ($search) {
            $query->where('tanggal', 'like', '%' . $search . '%');
        }
    
        // Urutkan berdasarkan ayam terbaru lalu tanggal naik
        $query->orderBy('monitoring_ayam.ayam_id', 'desc')
              ->orderBy('monitoring_ayam.tanggal', 'asc');
    
        $data = $query->paginate(50);
    
        return view('pages.inventory.monitoring.index', [
            'data' => $data,
            'search' => $search,
            'ayams' => $ayams,
            'ayams' => Ayam::all(),
            'id_ayam' => $id_ayam, // Pastikan ini tetap tersimpan di filter
            'kandangs' => \App\Models\Kandang::all(),
        ]);
    }
    
    public function create()
{
    $ayams = \App\Models\Ayam::with('kandang')->get(); // <- fix relasi kandang
    $kandangs = Kandang::all();

    return view('pages.inventory.monitoring.add', [
        'ayams' => $ayams,
        'kandangs' => $kandangs
    ]);
}


  public function store(Request $request)
    {
        // Ambil data ayam beserta kandang
        $ayam = Ayam::with('kandang')->findOrFail($request->ayam_id);
        $jumlahSkat = $ayam->kandang->jumlah_skat ?? 4;

        // Validasi dasar
        $rules = [
            'ayam_id' => 'required|exists:ayam,id_ayam',
            'tanggal_monitoring' => 'required|date|before_or_equal:today',
        ];

        // Validasi dinamis berdasarkan jumlah skat kandang
        for ($i = 1; $i <= $jumlahSkat; $i++) {
            $rules["skat_{$i}_bw"] = 'required|numeric|min:0|max:3500';
        }

        $validated = $request->validate($rules);

        // Hitung usia ayam berdasarkan tanggal masuk
        $tanggalMonitoring = Carbon::parse($validated['tanggal_monitoring']);
        $tanggalMasuk = Carbon::parse($ayam->tanggal_masuk);
        $ageDay = $tanggalMonitoring->diffInDays($tanggalMasuk);

        // Cek apakah data monitoring hari ini sudah ada
        $existing = MonitoringAyam::where('ayam_id', $validated['ayam_id'])
            ->where('age_day', $ageDay)
            ->first();

        try {
            $service = app(MonitoringGeneratorService::class);

            if ($existing) {
                // Update manual jika sudah ada data
                $service->updateManualMonitoring($existing->id, $validated);
                $message = "Data monitoring sampling hari ke-{$ageDay} berhasil diupdate";
            } else {
                // Simpan manual baru
                $service->storeManualMonitoring($validated);
                $message = 'Data monitoring sampling berhasil ditambahkan';
            }

            return redirect()->route('inventory.monitoring.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors('Gagal menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

/**
 * Update method yang sudah diperbaiki
 */
public function update(Request $request, $id)
{
    $monitoring = MonitoringAyam::with('ayam.kandang')->findOrFail($id);
    $jumlahSkat = $monitoring->ayam->kandang->jumlah_skat ?? 4;
    
    $rules = [];
    $messages = [];

    // Validasi berat sampel ayam dalam gram
    for ($i = 1; $i <= $jumlahSkat; $i++) {
        $rules["skat_{$i}_bw"] = 'required|numeric|min:0|max:3500'; // max 3500 gram
        $messages["skat_{$i}_bw.required"] = "Berat sampel ayam skat {$i} wajib diisi";
        $messages["skat_{$i}_bw.numeric"] = "Berat sampel ayam skat {$i} harus berupa angka";
        $messages["skat_{$i}_bw.min"] = "Berat sampel ayam skat {$i} tidak boleh negatif";
        $messages["skat_{$i}_bw.max"] = "Berat sampel ayam skat {$i} maksimal 3500 gram";
    }

    $validated = $request->validate($rules, $messages);

    DB::beginTransaction();
    try {
        $service = new MonitoringGeneratorService();
        $service->updateManualMonitoring($id, $validated);

        DB::commit();
        return redirect()->route('inventory.monitoring.index')
            ->with('success', 'Data monitoring sampling berhasil diupdate.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors('Gagal update: ' . $e->getMessage())
            ->withInput();
    }
}

    public function print(Request $request) 
{
    $query = MonitoringAyam::query();
    
    // Gunakan join yang sama seperti di method index
    $query->join('ayam', 'monitoring_ayam.ayam_id', '=', 'ayam.id_ayam')
          ->join('kandang', 'ayam.kandang_id', '=', 'kandang.id_kandang');
    
    // Sesuaikan where clause dengan nama kolom yang benar
    if ($request->id_ayam) {
        $query->where('ayam_id', $request->id_ayam);
    }
    
    if ($request->id_kandang) {
        $query->where('ayam.kandang_id', $request->id_kandang);
    }
    
    $data = $query->get();
    
    return view('pages.inventory.monitoring.print', [
        'data' => $data,
        'periode' => $request->id_ayam ? Ayam::find($request->id_ayam)->periode : 'Semua Periode',
        'kandang' => $request->id_kandang ? Kandang::find($request->id_kandang)->nama_kandang : 'Semua Kandang'
    ]);
}   
}
