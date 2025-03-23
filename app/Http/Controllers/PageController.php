<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Helpers\GeneralHelper;
use App\Http\Requests\UpdateConfigRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Attachment;
use App\Models\Barangkeluar;
use App\Models\Config;
use App\Models\Disposition;
use App\Models\Letter;
use App\Models\User;
use App\Models\Barangmasuk;
use App\Models\DetailBarangKeluar;
use App\Models\Permintaan;
use App\Models\Detailbarangmasuk;
use App\Models\Stok;
use App\Models\Barangg;
use App\Models\Detailstok;
use App\Models\Satuan;
use App\Models\Suplier;
use App\Models\Suplierr;
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
        // Mengambil data ayam untuk filter periode
        $ayams = Ayam::select('id_ayam', 'periode')->get();
        // Mengambil data kandang
        $kandangs = Kandang::select('id_kandang', 'nama_kandang')->get();

        // Data dashboard lainnya (contoh perhitungan)
        $todayayammasuk = Ayam::whereDate('tanggal_masuk', Carbon::today())
            ->sum('qty_ayam');

        $yesterdayayammasuk = Ayam::whereDate('tanggal_masuk', Carbon::yesterday())
            ->sum('qty_ayam');

        $percentageChange = $yesterdayayammasuk > 0 
            ? (($todayayammasuk - $yesterdayayammasuk) / $yesterdayayammasuk) * 100 
            : 0;

        $todayayammati = AyamMati::whereDate('tanggal_mati', Carbon::today())
            ->sum('quantity_mati');

        $yesterdayayammati = AyamMati::whereDate('tanggal_mati', Carbon::yesterday())
            ->sum('quantity_mati');

        $percentageChangekeluar = $yesterdayayammati > 0 
            ? (($todayayammati - $yesterdayayammati) / $yesterdayayammati) * 100 
            : 0;

        $todayayammatiCount = AyamMati::today()->count();
        $yesterdayayammatiCount = AyamMati::yesterday()->count();
        $todaypanen = Panen::today()->count();
        $yesterdaypanen = Panen::yesterday()->count();
        $todayayammasukCount = Ayam::today()->count();
        $yesterdayayammasukCount = Ayam::yesterday()->count();

        return view('pages.dashboard', [
            'greeting' => GeneralHelper::greeting(),
            'currentDate' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
            'todayayammati' => $todayayammatiCount,
            'yesterdayayammati' => $yesterdayayammatiCount,
            'todaypanen' => $todaypanen,
            'yesterdaypanen' => $yesterdaypanen,
            'todayayammasuk' => $todayayammasukCount,
            'yesterdayayammasuk' => $yesterdayayammasukCount,
            'activeUser' => User::active()->count(),
            'stokpakan' => MonitoringPakanDetail::sum('masuk'),
            'pakan' => Pakan::count(),
            'abk' => Abk::count(),
            'kandang' => Kandang::count(),
            'percentageIncomingLetter' => GeneralHelper::calculateChangePercentage($todayayammasuk, $yesterdayayammasuk),
            'percentageOutgoingLetter' => GeneralHelper::calculateChangePercentage($yesterdayayammati, $todayayammati),
            'percentageDispositionLetter' => GeneralHelper::calculateChangePercentage($yesterdaypanen, $todaypanen),
            'todayayammasuk' => $todayayammasukCount,
            'percentageChange' => round($percentageChange, 2),
            'todayayammati' => $todayayammati,
            'percentageChangekeluar' => round($percentageChangekeluar, 2),
            // Kirim data filter ke view
            'ayams' => Ayam::orderBy('id_ayam', 'desc')->get(), // Urutkan ayam berdasarkan yang terbaru
            'kandangs' => $kandangs,
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
    public function getChartData(Request $request)
    {
        try {
            // Ambil parameter filter
            $id_ayam = $request->input('id_ayam');
            $id_kandang = $request->input('id_kandang');
            
            // Log untuk debugging
            Log::info('Chart Data Request', [
                'id_ayam' => $id_ayam,
                'id_kandang' => $id_kandang
            ]);
            
            // Query untuk data dari tabel populasi
            $query = DB::table('populasi')
                ->join('ayam', 'populasi.populasi', '=', 'ayam.id_ayam');
                
            if ($id_ayam) {
                $query->where('ayam.id_ayam', $id_ayam);
            }
            
            if ($id_kandang) {
                $query->where('ayam.kandang_id', $id_kandang);
            }
            
            // Ambil data berdasarkan hari (day)
            $data = $query->select(
                    'populasi.day', 
                    'populasi.tanggal',
                    'populasi.qty_mati',
                    'populasi.qty_panen',
                    'populasi.total as total_populasi' 
                    )
                ->orderBy('populasi.day', 'asc')
                ->get();
            
            // Jika tidak ada data
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data untuk filter yang dipilih',
                    'labels' => [],
                    'qty_mati_series' => [],
                    'qty_panen_series' => [],
                    'populasi_series' => [],
                    'total_mati' => 0,
                    'total_panen' => 0
                ]);
            }
            
            // Siapkan data untuk chart
            $labels = [];
            $qtyMatiSeries = [];
            $qtyPanenSeries = [];
            $populasiSeries = [];
            $totalMati = 0;
            $totalPanen = 0;
            
            foreach ($data as $item) {
                $labels[] = 'Hari ' . $item->day . ' (' . Carbon::parse($item->tanggal)->format('d/m') . ')';
                $qtyMatiSeries[] = (int)$item->qty_mati;
                $qtyPanenSeries[] = (int)$item->qty_panen;
                $populasiSeries[] = (int)$item->total_populasi; // gunakan alias
                
                $totalMati += (int)$item->qty_mati;
                $totalPanen += (int)$item->qty_panen;
            }
            
            return response()->json([
                'success' => true,
                'labels' => $labels,
                'qty_mati_series' => $qtyMatiSeries,
                'qty_panen_series' => $qtyPanenSeries,
                'populasi_series' => $populasiSeries,
                'total_mati' => $totalMati,
                'total_panen' => $totalPanen
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chart Data Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

}
