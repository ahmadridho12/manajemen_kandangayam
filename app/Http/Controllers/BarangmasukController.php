<?php

namespace App\Http\Controllers;

use App\Models\Barangmasuk;
use App\Models\Detailbarangmasuk;
use App\Models\Barangg;
use App\Models\Kategoribm;
use App\Models\Suplierr;
use App\Models\Kategori;
use App\Models\Stok;
use App\Models\Detailstok;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BarangmasukController extends Controller
{

    public function index(Request $request)
{
    $search = $request->input('search');
    // Ambil semua data barang masuk, dengan opsi pencarian
    $query = Barangmasuk::query();

    // Jika ada query pencarian, tambahkan filter
    if ($search) {
        $query->where('jumlah', 'like', '%' . $search . '%');
        // Ganti 'jumlah' dengan nama kolom yang sesuai di tabel Anda
    }

    // Cek apakah ada permintaan untuk pengurutan
    if ($request->has('sort') && $request->has('order')) {
        $sortField = $request->input('sort');
        $sortOrder = $request->input('order');
        $query->orderBy($sortField, $sortOrder);
    } else {
        // Jika tidak ada pengurutan, urutkan berdasarkan tanggal masuk secara default
        $query->orderBy('tgl_masuk', 'desc');
    }

    // Ambil data barang masuk dengan paginasi
    $BarangmasukList = $query->with('suplier') // Pastikan ada relasi dengan model Suplier
                              ->paginate(20); // Ganti 20 dengan jumlah data per halaman yang diinginkan

    return view('pages.transaksi.barangmasuk.index', [
        'BarangmasukList' => $BarangmasukList,
        'search' => $search,
    ]);
}
    public function create()
    {
        $suplierr = Suplierr::all();
        $barangg = Barangg::all();
        $kategoribm = Kategoribm::all();

        return view('pages.transaksi.barangmasuk.add', compact('suplierr', 'barangg', 'kategoribm'));
    }

    public function store(Request $request)
{
    $request->validate([
        'suplier_id' => 'required|exists:suplierr,id_suplier', // Pastikan nama tabel benar
        'barang' => 'required|array',
        'tgl_masuk' => 'required|date',
        'keterangan' => 'required|string',
    ]);

    // Generate no_transaksi otomatis
    $noTransaksi = $this->generateNoTransaksi($request->tgl_masuk);

    // Simpan data barang masuk
    $barangMasuk = Barangmasuk::create([
        'suplier_id' => $request->suplier_id,
        'no_transaksi' => $noTransaksi,
        'tgl_masuk' => Carbon::parse($request->tgl_masuk)->format('Y-m-d'), // Menggunakan tanggal dari request
        'keterangan' => $request->keterangan
    ]);

    foreach ($request->barang as $barang) {
        $idBarang = $barang['id'] ?? null; // Pastikan ini mengambil id_barang dengan benar
        $jumlahBarang = $barang['jumlah'] ?? 0;
        $hargaSebelumPpn = $barang['harga_sebelum_ppn'] ?? 0;
        $kategoriPpnId = $barang['kategori_ppn_id'];

        // Access id_jenis from related tables
        $barangg = Barangg::with('jenis')->find($idBarang);
        $idJenis = $barangg ? $barangg->id_jenis : null;

        // Hitung PPN
        $kategoriPpn = Kategoribm::find($kategoriPpnId);

        // Pastikan kategori PPN ditemukan
        if (!$kategoriPpn) {
            return redirect()->back()->withErrors(['msg' => 'Kategori PPN tidak ditemukan.']);
        }

        // Ambil persentase PPN
        $ppnPersentase = $kategoriPpn->ppn; // Pastikan ini menggunakan kolom yang benar

        // Hitung nilai PPN
        $ppn = $hargaSebelumPpn * ($ppnPersentase / 100);
        $hargaSetelahPpn = $hargaSebelumPpn + $ppn;
        $totalSetelahPpn = $hargaSetelahPpn * $jumlahBarang;

        // Simpan detail barang masuk
        $detailBarangMasuk = Detailbarangmasuk::create([
            'barang_masuk_id' => $barangMasuk->id_masuk,
            'id_barang' => $idBarang,
            'id_jenis' => $idJenis,
            'jumlah' => $jumlahBarang,
            'harga_sebelum_ppn' => $hargaSebelumPpn,
            'kategori_ppn_id' => $kategoriPpnId,
            'harga_setelah_ppn' => $hargaSetelahPpn,
            'total_setelah_ppn' => $totalSetelahPpn,
            'tanggal_detailmasuk' => Carbon::parse($request->tgl_masuk)->format('Y-m-d'), // Menggunakan Carbon untuk mengonversi
        ]);

        // Update stok barang
        $stok = Stok::where('id_barang', $idBarang)->first();

        if ($stok) {
            // Jika stok sudah ada, perbarui jumlahnya
            $stok->qty += $jumlahBarang;
            $stok->save();

            // Simpan detail stok (stok batch per detail barang masuk)
            Detailstok::create([
                'stok_id' => $stok->id_stok,
                'detailmasuk_id' => $detailBarangMasuk->id_detailmasuk,
                'barang_id' => $idBarang,
                'qty_stok' => $jumlahBarang,
                'harga' => $hargaSetelahPpn,
                'total' => $hargaSetelahPpn * $jumlahBarang,
            ]);
        } else {
            // Jika stok belum ada, buat stok baru
            $newStok = Stok::create([
                'id_barang' => $idBarang,
                'qty' => $jumlahBarang,
            ]);

            // Simpan detail stok dengan ID stok yang baru dibuat
            Detailstok::create([
                'stok_id' => $newStok->id_stok,
                'detailmasuk_id' => $detailBarangMasuk->id_detailmasuk,
                'barang_id' => $idBarang,
                'qty_stok' => $jumlahBarang,
                'total' => $hargaSetelahPpn * $jumlahBarang,
                'harga' => $hargaSetelahPpn,
            ]);
        }
    }

      return redirect()->route('transaksi.barangmasuk.index')->with('success', 'Barang masuk berhasil ditambahkan.');
  }

  private function generateNoTransaksi($tglMasuk)
{
    // Mengambil bulan dan tahun dari tgl_masuk
    $bulan = Carbon::parse($tglMasuk)->format('m');
    $tahun = Carbon::parse($tglMasuk)->format('Y');
    $prefix = 'OP/PERUMDAM';

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

    // Cari nomor transaksi terbesar untuk bulan dan tahun yang spesifik
    $lastTransactionNumber = Barangmasuk::where(function($query) use ($bulan, $tahun) {
            $query->whereMonth('tgl_masuk', $bulan)
                  ->whereYear('tgl_masuk', $tahun);
        })
        ->orderBy('no_transaksi', 'desc')
        ->first();

    if ($lastTransactionNumber) {
        // Mengambil nomor transaksi dan memisahkannya
        $parts = explode('/', $lastTransactionNumber->no_transaksi);
        $lastNumber = (int)$parts[0];
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    // Format nomor dengan leading zeros (2 digit)
    $formattedNumber = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    
    // Mengonversi bulan ke angka Romawi
    $romanBulan = convertToRoman($bulan);

    // Buat nomor transaksi lengkap
    $noTransaksi = "{$formattedNumber}/{$prefix}/{$romanBulan}/{$tahun}";

    // Periksa apakah nomor transaksi sudah ada
    while (Barangmasuk::where('no_transaksi', $noTransaksi)->exists()) {
        $nextNumber++;
        $formattedNumber = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        $noTransaksi = "{$formattedNumber}/{$prefix}/{$romanBulan}/{$tahun}";
    }

    // Debug: Log nomor transaksi final
    Log::info('Generated Transaction Number: ', ['noTransaksi' => $noTransaksi]);

    return $noTransaksi;
}
    public function keluar(Request $request)
    {
    $request->validate([
        'barang_id' => 'required|exists:barangg,id_barang',
        'jumlah' => 'required|integer|min:1',
    ]);

    $barang = Barangg::find($request->barang_id);
    $stok = Stok::where('id_barang', $barang->id_barang)->first();

    if ($stok && $stok->qty >= $request->jumlah) {
        $stok->qty -= $request->jumlah; // Kurangi jumlah stok
        $stok->save();

        // Simpan detail pengeluaran barang sesuai kebutuhan
        // Misalnya, bisa disimpan di tabel lain untuk histori keluar barang
    } else {
        return redirect()->back()->withErrors(['msg' => 'Stok tidak mencukupi.']);
    }

    return redirect()->route('transaksi.barangkeluar.index')->with('success', 'Barang keluar berhasil.');
}
public function show($id_masuk)
    {
        // Mengambil permintaan dengan id yang diberikan, beserta relasinya
        $barangMasuk = Barangmasuk::with('detail',)
            ->findOrFail($id_masuk);
    
        return view('pages.transaksi.barangmasuk.show', compact('barangMasuk'));
    }

}