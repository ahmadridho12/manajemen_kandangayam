<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permintaan;
use App\Models\Jenis;
use App\Models\Barangg;
use App\Models\DetailPermintaan;
use App\Models\User;
use App\Models\Bagian;
use App\Models\Stok;
use App\Models\DetailBarangKeluar;
use App\Models\BarangKeluar;
use App\Models\Detailstok;
use App\Models\DetailBarangMasuk;
use App\Models\Kategori;
use App\Models\Tipe;
use Illuminate\Support\Carbon;



use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;


class PermintaanController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search'); // Jika ada pencarian
    
    // Buat query dasar
    $query = Permintaan::with(['user', 'bagiann', 'tipe']);

    // Tambahkan kondisi berdasarkan role
    if (auth()->user()->role === 'staf') {
        // Jika role staf, hanya tampilkan permintaan milik sendiri
        $query->where('id_user', auth()->user()->id);
    }

    // Tambahkan pencarian
    $query->when($search, function($q) use ($search) {
        return $q->where('no_trans', 'like', '%' . $search . '%')
                 ->orWhere('keterangan', 'like', '%' . $search . '%')
                 ->orWhereHas('bagiann', function($subQ) use ($search) {
                     $subQ->where('nama_bagian', 'like', '%' . $search . '%');
                 })
                 ->orWhereHas('tipe', function($subQ) use ($search) {
                     $subQ->where('nama_tipe', 'like', '%' . $search . '%');
                 });
    });

    // Urutkan dan ambil data
    $data = $query->orderBy('tgl_permintaan', 'desc')
                  ->paginate(25);

    $bagians = Bagian::all();
    $kategoris = Kategori::all();

    return view('pages.transaksi.permintaan.index', compact('data', 'search', 'bagians', 'kategoris'));
}
    
    public function create()
    {
        $user = User::all();
        $bagians = Bagian::all();
        $jenisa = Jenis::all();
        $barangs = Barangg::all();
        $kategoris = Kategori::all();
        
        $stokData = DB::table('detail_stok')
            ->select('barang_id', DB::raw('SUM(qty_stok) as total_stok'))
            ->groupBy('barang_id')
            ->get();

        Log::info('Stok Data Raw:', $stokData->toArray());

        $stokDataArray = $stokData->mapWithKeys(function($item) {
            return [$item->barang_id => $item->total_stok];
        });

        return view('pages.transaksi.permintaan.add', [
            'user' => $user,
            'bagians' => $bagians,
            'jenisa' => $jenisa,
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'stokData' => $stokDataArray
        ]);
    }


public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'keterangan' => 'required|string',
            'tgl_permintaan' => 'required|date',
            'nama_bagian' => 'required|string',
            'nama_tipe' => 'required|string',
            'barang' => 'required|array',
            'barang.*.id_barang' => 'required|exists:barangg,id_barang',
            'barang.*.qty' => 'required|integer|min:1',
            'tanggal_persetujuan' => 'nullable|date',
            'id_user_persetujuan' => 'nullable|exists:users,id',
        ]);
        
        // Ambil ID bagian
        $bagianId = Bagian::where('nama_bagian', $request->nama_bagian)->value('id_bagian');
        if (!$bagianId) {
            return redirect()->back()->withErrors(['nama_bagian' => 'Bagian tidak ditemukan.']);
        }

        // Ambil ID Tipe
        $kategoriId = Kategori::where('nama_tipe', $request->nama_tipe)->value('id_tipe');
        if (!$kategoriId) {
            return redirect()->back()->withErrors(['nama_tipe' => 'Tipe tidak ditemukan.']);
        }

        $status_persetujuan = (auth()->user()->role === 'staf') ? 'pending' : null;
        $tanggalPermintaan = Carbon::parse($request->tgl_permintaan)->format('Y-m-d');

        // Simpan data ke tabel permintaan
        $permintaan = new Permintaan();
        $permintaan->keterangan = $request->keterangan;
        $permintaan->tgl_permintaan = $tanggalPermintaan;
        $permintaan->bagian = $bagianId;
        $permintaan->tipe_id = $kategoriId;
        $permintaan->id_user = auth()->user()->id;
        $permintaan->status_persetujuan = $status_persetujuan;
        $permintaan->tanggal_persetujuan = $request->tanggal_persetujuan;
        $permintaan->id_user_persetujuan = $request->id_user_persetujuan;

        // Generate nomor transaksi berdasarkan tanggal permintaan jika bukan pending
        if ($status_persetujuan !== 'pending') {
            $permintaan->no_trans = $this->generateNoTransaksi($tanggalPermintaan);
        }

        $permintaan->save();

        // Simpan detail permintaan
        foreach ($request->barang as $barang) {
            $detail = new DetailPermintaan();
            $detail->id_permintaan = $permintaan->id_permintaan;
            $detail->id_barang = $barang['id_barang'];
            $detail->qty = $barang['qty'];
            $detail->tanggal_detailpermintaan = $tanggalPermintaan;

            $detailMasuk = DetailBarangMasuk::where('id_barang', $barang['id_barang'])->first();
            $detail->harga = $detailMasuk ? $detailMasuk->harga_setelah_ppn : 0;

            $detail->save();
        }

        if (in_array(auth()->user()->role, ['admin', 'kasubagumumdangudang', 'staff'])) {
            $this->createBarangKeluar($permintaan, $tanggalPermintaan);
        }

        return redirect()->route('transaksi.permintaan.index')->with('success', 'Permintaan berhasil disimpan!');
    } catch (\Exception $e) {
        Log::error('Error in store method: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function approveRequest(Request $request, $id)
{
    $permintaan = Permintaan::find($id);

    if (!$permintaan) {
        return redirect()->back()->with('error', 'Permintaan tidak ditemukan.');
    }

    if ($permintaan->status_persetujuan !== 'pending') {
        return redirect()->back()->with('error', 'Permintaan sudah tidak dalam status pending.');
    }

    if (auth()->check() && (auth()->user()->role === 'admin' || 
        auth()->user()->role === 'kasubagumumdangudang' || 
        auth()->user()->role === 'staff')) {
        DB::transaction(function () use ($permintaan) {
            // Generate nomor transaksi berdasarkan tanggal permintaan saat approve
            $permintaan->no_trans = $this->generateNoTransaksi($permintaan->tgl_permintaan);
            $permintaan->status_persetujuan = null;
            $permintaan->tanggal_persetujuan = now();
            $permintaan->id_user_persetujuan = auth()->user()->id;
            $permintaan->save();

            $tanggalPermintaan = Carbon::parse($permintaan->tgl_permintaan)->format('Y-m-d');
            $this->createBarangKeluar($permintaan, $tanggalPermintaan);
        });

        return redirect()->back()->with('success', 'Permintaan disetujui dan diproses.');
    }

    return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menyetujui permintaan ini.');
}

private function generateNoTransaksi($tanggalPermintaan)
{
    $targetDate = Carbon::parse($tanggalPermintaan);
    $bulan = $targetDate->format('m');
    $tahun = $targetDate->format('Y');

    // Fungsi untuk mengonversi angka bulan ke angka Romawi
    function convertToRoman($month) {
        $romanNumerals = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $romanNumerals[(int)$month];
    }

    // Cari nomor transaksi terakhir untuk bulan dan tahun yang spesifik
    $lastTransaction = DB::table('permintaan')
        ->whereYear('tgl_permintaan', $tahun)
        ->whereMonth('tgl_permintaan', $bulan)
        ->whereNotNull('no_trans')
        ->orderByRaw("CAST(SUBSTRING_INDEX(no_trans, '/', 1) AS UNSIGNED) DESC")
        ->first();

    if ($lastTransaction) {
        $lastNoTransaksi = $lastTransaction->no_trans;
        $lastNumber = (int)explode('/', $lastNoTransaksi)[0];
        $nextNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
    } else {
        $nextNumber = '01';
    }

    $romanBulan = convertToRoman($bulan);

    return "{$nextNumber}/DPPB/PERUMDAM/{$romanBulan}/{$tahun}";
}

private function createBarangKeluar($permintaan, $tanggalPermintaan)
{
    $detailPermintaan = DetailPermintaan::where('id_permintaan', $permintaan->id_permintaan)->get();

    $barangKeluar = BarangKeluar::create([
        'no_transaksi' => str_replace('DPPB', 'BPP', $permintaan->no_trans),
        'id_permintaan' => $permintaan->id_permintaan,
        'tanggal_keluar' => $tanggalPermintaan,
    ]);

    DB::transaction(function () use ($detailPermintaan, $barangKeluar, $tanggalPermintaan) {
        foreach ($detailPermintaan as $detail) {
            $detailsToSave = $this->reduceStockFIFO($detail->id_barang, $detail->qty);

            foreach ($detailsToSave as $stockDetail) {
                DetailBarangKeluar::create([
                    'id_barangkeluar' => $barangKeluar->id_keluar,
                    'id_barang' => $stockDetail['id_barang'],
                    'jumlah' => $stockDetail['jumlah'],
                    'harga' => $stockDetail['harga'],
                    'total' => $stockDetail['jumlah'] * $stockDetail['harga'],
                    'tanggal_detailkeluar' => $tanggalPermintaan,
                ]);
            }
        }
    });
}
    
        private function reduceStockFIFO($id_barang, $qty)
        {
            $detailStok = Detailstok::where('barang_id', $id_barang)
                ->orderBy('created_at')
                ->get();
    
            $totalStokTersedia = $detailStok->sum('qty_stok');
    
            if ($totalStokTersedia < $qty) {
                throw new \Exception("Stok tidak cukup untuk memenuhi permintaan.");
            }
    
            $detailsToSave = [];
    
            foreach ($detailStok as $detail) {
                if ($qty <= 0) {
                    break;
                }
    
                Log::info("Detail Stok ID: {$detail->id_detailstok}, Qty Stok: {$detail->qty_stok}, Qty Permintaan: $qty");
    
                if ($detail->qty_stok >= $qty) {
                    $detailsToSave[] = [
                        'id_barang' => $id_barang,
                        'jumlah' => $qty,
                        'harga' => $detail->harga_setelah_ppn,
                    ];
    
                    $detail->qty_stok -= $qty;
                    $detail->save();
    
                    if ($detail->qty_stok == 0) {
                        $detail->delete();
                    }
    
                    $qty = 0;
                } else {
                    $detailsToSave[] = [
                        'id_barang' => $id_barang,
                        'jumlah' => $detail->qty_stok,
                        'harga' => $detail->harga_setelah_ppn,
                    ];
    
                    $qty -= $detail->qty_stok;
                    $detail->delete();
                }
            }
    
            if ($qty > 0) {
                throw new \Exception("Stok tidak cukup untuk memenuhi permintaan. Total qty yang berhasil dikurangi.");
            }
    
            return $detailsToSave;
        }
    
    
    
    public function show($id_permintaan)
    {
        // Mengambil permintaan dengan id yang diberikan, beserta relasinya
        $permintaan = Permintaan::with('detailpermintaan.barang.satuan', 'bagiann')
            ->findOrFail($id_permintaan);
    
        return view('pages.transaksi.permintaan.show', compact('permintaan'));
    }
        public function print($id_permintaan)
    {
        // Ambil data permintaan berdasarkan ID
        $permintaan = Permintaan::with(['user', 'bagiann', 'detailPermintaan.barang'])->findOrFail($id_permintaan);
        
        // Mengambil tanggal saat ini
        $currentDate = now()->format('d-m-Y');

        return view('pages.transaksi.permintaan.print', compact('permintaan', 'currentDate'));
    }




    public function update(Request $request, $id_permintaan)
{
    // Validasi input
    $request->validate([
        
        'keterangan' => 'required|string|max:255',
        'tgl_permintaan' => 'required',
        'bagian' => 'required',
        'tipe_id' => 'required',
        'barang.*.id_barang' => 'required|exists:barangg,id_barang',
        'barang.*.qty' => 'required|integer|min:1',
    ]);

    // Temukan data permintaan
    $permintaan = Permintaan::find($id_permintaan);
    if (!$permintaan) {
        return redirect()->route('transaksi.permintaan.index')->with('error', 'Permintaan tidak ditemukan.');
    }

    // Simpan data permintaan yang diperbarui
    $permintaan->update([
        'keterangan' => $request->keterangan,
        'tgl_permintaan' => $request->tgl_permintaan,
        'bagian' => $request->bagian,
        'tipe_id' => $request->tipe_id,
    ]);

    // Ambil detail permintaan yang ada sebelumnya
    $detailPermintaanLama = DetailPermintaan::where('id_permintaan', $id_permintaan)->get();

    // Loop untuk memproses barang yang baru
    foreach ($request->barang as $barangBaru) {
        // Ambil qty yang lama dari detail permintaan lama
        $detailLama = $detailPermintaanLama->where('id_barang', $barangBaru['id_barang'])->first();

        if ($detailLama) {
            // Hitung perubahan qty
            $qtySebelumnya = $detailLama->qty;
            $qtyBaru = $barangBaru['qty'];

            if ($qtyBaru < $qtySebelumnya) {
                // Jika qty baru lebih sedikit, kembalikan stok yang hilang
                $stokYangDikembalikan = $qtySebelumnya - $qtyBaru;
                $this->kembalikanStok($barangBaru['id_barang'], $stokYangDikembalikan);
            } elseif ($qtyBaru > $qtySebelumnya) {
                // Jika qty baru lebih banyak, kurangi stok yang baru
                $stokYangKurang = $qtyBaru - $qtySebelumnya;
                $this->kurangiStok($barangBaru['id_barang'], $stokYangKurang);
            }

            // Update qty di detail permintaan
            $detailLama->qty = $qtyBaru;
            $detailLama->save();
        } else {
            // Jika barang tidak ada di detail permintaan, buat entry baru
            DetailPermintaan::create([
                'id_permintaan' => $id_permintaan,
                'id_barang' => $barangBaru['id_barang'],
                'qty' => $barangBaru['qty'],
            ]);
        }
    }

    // Redirect atau tampilkan pesan sukses
    return redirect()->route('transaksi.permintaan.index')->with('success', 'Permintaan barang berhasil diperbarui!');
}

// Fungsi untuk mengembalikan stok
private function kembalikanStok($idBarang, $qty)
{
    // Cari stok yang sudah dikeluarkan dan tambahkan kembali
    $stok = DetailStok::where('barang_id', $idBarang)
                      ->orderBy('created_at', 'asc')
                      ->first();

    if ($stok) {
        $stok->qty_stok += $qty;
        $stok->save();
    } else {
        throw new \Exception("Stok tidak ditemukan untuk barang ID: {$idBarang}.");
    }
}

// Fungsi untuk mengurangi stok
private function kurangiStok($idBarang, $qty)
{
    // Kurangi stok sesuai qty yang dikeluarkan
    $stok = DetailStok::where('barang_id', $idBarang)
                      ->orderBy('created_at', 'asc')
                      ->first();

    if ($stok) {
        if ($stok->qty_stok >= $qty) {
            $stok->qty_stok -= $qty;
            $stok->save();
        } else {
            throw new \Exception("Stok tidak cukup untuk mengurangi jumlah.");
        }
    } else {
        throw new \Exception("Stok tidak ditemukan untuk barang ID: {$idBarang}.");
    }
}
 

    public function getBagians()
    {
        $bagians = Bagian::all();
        return response()->json(['bagians' => $bagians]);
    }
    
    public function getKategoris()
    {
        $kategoris = Kategori::all();
        return response()->json(['kategoris' => $kategoris]);
    }


}