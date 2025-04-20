<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Helpers\GeneralHelper;
use App\Http\Requests\UpdateConfigRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Attachment;

use App\Models\Config;
use App\Models\Disposition;
use App\Models\Letter;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\NoReturn;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\UpdateUser; // Pastikan ini benar
use App\Models\Abk;
use App\Models\Ayam;
use App\Models\AyamMati;
use App\Models\Kandang;
use App\Models\MonitoringPakanDetail;
use App\Models\Pakan;
use App\Models\Panen;

// use App\Http\Requests\UpdateUserRequest; // Pastikan ini sesuai dengan nama kelas Anda
use Illuminate\Http\Request; // Pastikan ini diimpor
class PageController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
{
    // Ambil parameter filter
    $idAyam = $request->query('id_ayam');
    $idKandang = $request->query('id_kandang');
    // $periode = $request->query('periode');

    $jumlahKandang = Kandang::count(); // Hitung jumlah kandang yang ada saat ini
    // $totalMasuk = DB::table('monitoring_pakan_detail')
    // ->whereBetween('created_at', [$periodeAwal, $periodeAkhir])
    // ->sum('masuk');
    $pakan = Pakan::count(); // total jenis pakan
    $abk = ABK::count(); // total ABK aktif

    $percentageIncomingLetter = 0; // misalnya belum dipakai, set default aja dulu
    $percentageOutgoingLetter = 0;


    // --- DAILY METRICS (Hari Ini vs Kemarin) ---
    $todayMasukCount = Ayam::today()->sum('qty_ayam');
    $yesterdayMasukCount = Ayam::yesterday()->sum('qty_ayam');
    $percentageChange = $yesterdayMasukCount > 0
        ? round((($todayMasukCount - $yesterdayMasukCount) / $yesterdayMasukCount) * 100, 2)
        : 0;

    $todayMatiCount = AyamMati::today()->sum('quantity_mati');
    $yesterdayMatiCount = AyamMati::yesterday()->sum('quantity_mati');
    $percentageChangekeluar = $yesterdayMatiCount > 0
        ? round((($todayMatiCount - $yesterdayMatiCount) / $yesterdayMatiCount) * 100, 2)
        : 0;

    $todayPanenCount = Panen::today()->sum('quantity');
    $yesterdayPanenCount = Panen::yesterday()->count();
    $percentageDispositionLetter = $yesterdayPanenCount > 0
        ? round((($todayPanenCount - $yesterdayPanenCount) / $yesterdayPanenCount) * 100, 2)
        : 0;

    // --- FILTERED METRICS (Per Periode + Kandang) ---
    // --- FILTERED METRICS (Per Periode + Kandang) ---
    $masukQ = Ayam::query()
    ->when($idAyam, fn($q) => $q->where('id_ayam', $idAyam)) // Menyaring berdasarkan id_ayam saja
    ->when($idKandang, fn($q) => $q->where('kandang_id', $idKandang)); // Menyaring berdasarkan id_kandang

    $matiQ = AyamMati::query()
    ->join('ayam', 'ayam_mati.ayam_id', '=', 'ayam.id_ayam')
    ->when($idAyam, fn($q) => $q->where('ayam_id', $idAyam)) // Menyaring berdasarkan id_ayam saja
    ->when($idKandang, fn($q) => $q->where('ayam.kandang_id', $idKandang)); // Menyaring berdasarkan id_kandang

    $panenQ = Panen::query()
    ->join('ayam', 'panen.ayam_id', '=', 'ayam.id_ayam')
    ->when($idAyam, fn($q) => $q->where('panen.ayam_id', $idAyam)) // Menyaring berdasarkan id_ayam saja
    ->when($idKandang, fn($q) => $q->where('ayam.kandang_id', $idKandang)); // Menyaring berdasarkan id_kandang

    $stokpakan = DB::table('monitoring_pakan_detail')
        ->join('ayam', 'monitoring_pakan_detail.ayam_id', '=', 'ayam.id_ayam')
        ->when($idAyam, fn($q) => $q->where('ayam.id_ayam', $idAyam))
        ->when($idKandang, fn($q) => $q->where('ayam.kandang_id', $idKandang))
        ->sum('masuk'); // Menjumlahkan semua nilai 'masuk'

    $mati = DB::table('ayam_mati')
        ->join('ayam', 'ayam_mati.ayam_id', '=', 'ayam.id_ayam')
        ->when($idAyam, fn($q) => $q->where('ayam.id_ayam', $idAyam))
        ->when($idKandang, fn($q) => $q->where('ayam.kandang_id', $idKandang))
        ->sum('quantity_mati'); // Menjumlahkan semua nilai 'masuk'



    $doc = DB::table('ayam')
    ->when($idAyam, fn($q) => $q->where('id_ayam', $idAyam))
    ->when($idKandang, fn($q) => $q->where('kandang_id', $idKandang))
    ->sum('qty_ayam'); // Jumlahkan total qty_ayam
    
// dd($periode);

    
    // Kalkulasi data yang difilter
    $filteredMasuk = $masukQ->sum('qty_ayam');
    $filteredMati = $matiQ->sum('quantity_mati');
    $filteredPanen = $panenQ->sum('panen.quantity');

    return view('pages.dashboard', [
        // daily
        'todayayammasuk' => $todayMasukCount,
        'todayayammati' => $todayMatiCount,
        'todaypanen' => $todayPanenCount,
        'percentageChange' => $percentageChange,
        'percentageChangekeluar' => $percentageChangekeluar,
        'percentageDispositionLetter' => $percentageDispositionLetter,

        // cards
        'doc' => $doc,
        'mati' => $mati,
        'stokpakan' => $stokpakan,
        'pakan' => $pakan,
        'abk' => $abk,
        'jumlahKandang' => $jumlahKandang,
        'percentageIncomingLetter' => $percentageIncomingLetter,
        'percentageOutgoingLetter' => $percentageOutgoingLetter,

        // filtered
        'filteredMasuk' => $filteredMasuk,
        'filteredMati' => $filteredMati,
        'filteredPanen' => $filteredPanen,
        'selectedAyam' => $idAyam,
        'selectedKandang' => $idKandang,
        // 'selectedPeriode' => $periode,

        // dropdown & greeting
        'ayams' => Ayam::orderBy('id_ayam', 'desc')->get(),
        'kandangs' => Kandang::all(),
        'greeting' => GeneralHelper::greeting(),
        'currentDate' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
    ]);
}


    /**
     * @param Request $request
     * @return View
     */
    public function profile(Request $request): View
    {
        return view('pages.profile', [
            'data' => auth()->user(),
        ]);
    }

    /**
     * @param UpdateUserRequest $request
     * @return RedirectResponse
     */
    public function profileUpdate(UpdateUserRequest $request): RedirectResponse
{
    try {
        $newProfile = $request->validated();
        dd($newProfile); // Tambahkan ini untuk mengecek isi validasi

        if ($request->hasFile('profile_picture')) {
            // DELETE OLD PICTURE
            $oldPicture = auth()->user()->profile_picture;
            if (str_contains($oldPicture, '/storage/avatars/')) {
                $url = parse_url($oldPicture, PHP_URL_PATH);
                Storage::delete(str_replace('/storage', 'public', $url));
            }

            // UPLOAD NEW PICTURE
            $filename = time() .
                '-' . $request->file('profile_picture')->getFilename() .
                '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->storeAs('public/avatars', $filename);
            $newProfile['profile_picture'] = asset('storage/avatars/' . $filename);
        }

        auth()->user()->update($newProfile); // Harus ada
        return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
        return back()->with('error', $exception->getMessage());
    }
}


    /**
     * @param Request $request
     * @return View
     */
    public function settings(Request $request): View
    {
        return view('pages.setting', [
            'configs' => Config::all(),
        ]);
    }

    /**
     * @param UpdateConfigRequest $request
     * @return RedirectResponse
     */
    public function settingsUpdate(UpdateConfigRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($request->validated() as $code => $value) {
                Config::where('code', $code)->update(['value' => $value]);
            }
            DB::commit();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function removeAttachment(Request $request): RedirectResponse
    {
        try {
            $attachment = Attachment::find($request->id);
            $oldPicture = $attachment->path_url;
            if (str_contains($oldPicture, '/storage/attachments/')) {
                $url = parse_url($oldPicture, PHP_URL_PATH);
                Storage::delete(str_replace('/storage', 'public', $url));
            }
            $attachment->delete();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
    public function getChartData(Request $request) {
        $id_ayam = $request->input('id_ayam');
        $id_kandang = $request->input('id_kandang');
        
        // Cek apakah ayam dengan ID tersebut ada
        $ayam = null;
        if ($id_ayam) {
            $ayam = Ayam::find($id_ayam);
            if (!$ayam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data ayam tidak ditemukan',
                    'labels' => [],
                    'qty_mati_series' => [],
                    'qty_panen_series' => [],
                    'populasi_series' => [],
                    'total_mati' => 0,
                    'total_panen' => 0,
                ]);
            }
        }
        
        $query = DB::table('populasi')
            ->join('ayam', 'ayam.id_ayam', '=', 'populasi.populasi')
            ->select(
                'populasi.tanggal',
                'populasi.qty_mati',
                'populasi.qty_panen',
                'populasi.total'
            );
        
        if ($id_ayam) {
            $query->where('ayam.id_ayam', $id_ayam);
        }
        
        if ($id_kandang) {
            $query->where('ayam.kandang_id', $id_kandang);
        }
        
        // Urut berdasarkan tanggal
        $data = $query->orderBy('populasi.tanggal')->get();
        
        if ($data->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada data untuk filter yang dipilih',
                'labels' => [],
                'qty_mati_series' => [],
                'qty_panen_series' => [],
                'populasi_series' => [],
                'total_mati' => 0,
                'total_panen' => 0,
            ]);
        }
        
        $labels = [];
        $qtyMatiSeries = [];
        $qtyPanenSeries = [];
        $populasiSeries = [];
        
        foreach ($data as $item) {
            // Format tanggal untuk label
            $tanggal = Carbon::parse($item->tanggal)->format('d/m/Y');
            $labels[] = $tanggal;
            $qtyMatiSeries[] = (int)$item->qty_mati;
            $qtyPanenSeries[] = (int)$item->qty_panen;
            $populasiSeries[] = (int)$item->total;
        }
        
        $totalMati = array_sum($qtyMatiSeries);
        $totalPanen = array_sum($qtyPanenSeries);
        
        return response()->json([
            'success' => true,
            'labels' => $labels,
            'qty_mati_series' => $qtyMatiSeries,
            'qty_panen_series' => $qtyPanenSeries,
            'populasi_series' => $populasiSeries,
            'total_mati' => $totalMati,
            'total_panen' => $totalPanen,
        ]);
    }


}
