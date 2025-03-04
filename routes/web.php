<?php

use App\Http\Controllers\AyamController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\BarangmasukController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DetailbarangmasukController;
use App\Http\Controllers\DetailbarangkeluarController;
use App\Http\Controllers\BarangkeluarController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\AyammatiController;
use App\Http\Controllers\PanenController;
use App\Http\Controllers\FormalController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MonitoringAyamController;
use App\Http\Controllers\MonitoringPakanController;
use App\Http\Controllers\PakanMasukController;
use App\Http\Controllers\PakanKeluarController;
use App\Http\Controllers\PakanTransferController;
use App\Http\Controllers\PerformaController;
use App\Http\Controllers\PerhitunganGajiController;


use App\Models\Barangkeluar;
use App\Models\Barangmasuk;
use App\Models\Permintaan;
use App\Models\Permission;
use App\Http\Controllers\TarifAirController;
use App\Models\MonitoringAyam;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'session.timeout'])->group(function () {
    Route::get('/', [\App\Http\Controllers\PageController::class, 'index'])->name('home');

    Route::resource('user', \App\Http\Controllers\UserController::class)
        ->except(['show', 'edit', 'create'])
        ->middleware(['role:admin']);

        Route::get('profile', [\App\Http\Controllers\PageController::class, 'profile'])
        ->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\PageController::class, 'profileUpdate'])
        ->name('profile.update');
    Route::put('profile/deactivate', [\App\Http\Controllers\PageController::class, 'deactivate'])
        ->name('profile.deactivate')
        ->middleware(['role:staff']);

    Route::get('settings', [\App\Http\Controllers\PageController::class, 'settings'])
        ->name('settings.show')
        ->middleware(['role:admin']);
    Route::put('settings', [\App\Http\Controllers\PageController::class, 'settingsUpdate'])
        ->name('settings.update')
        ->middleware(['role:admin']);

    Route::delete('attachment', [\App\Http\Controllers\PageController::class, 'removeAttachment'])
        ->name('attachment.destroy');

    // Rute admin
    Route::get('/admin', [UserController::class, 'index'])->name('admin.index');

    // Rute transaksi
    Route::prefix('transaction')->as('transaction.')->group(function () {
        Route::resource('incoming', \App\Http\Controllers\IncomingLetterController::class);
        Route::resource('outgoing', \App\Http\Controllers\OutgoingLetterController::class);
        Route::resource('{letter}/disposition', \App\Http\Controllers\DispositionController::class)->except(['show']);
    });

    // Rute inventory
    Route::prefix('inventory')->as('inventory.')->group(function () {
        Route::resource('/category', \App\Http\Controllers\CategoryController::class);
        Route::resource('/goods', \App\Http\Controllers\BarangController::class);
        Route::get('/inventory/generate-kode-barang/{id_jenis}', [BarangController::class, 'generateKodeBarang']);
        Route::post('/barangg', [BarangController::class, 'store']);
        Route::get('/get-satuans', [BarangController::class, 'getSatuans'])->name('getSatuans');
        Route::resource('/kategori', \App\Http\Controllers\KategoriController::class);
        Route::resource('/stok', \App\Http\Controllers\StokController::class);
        Route::resource('/populasi', \App\Http\Controllers\PopulasiController::class);
        Route::get('/inventory/populasi/print', [\App\Http\Controllers\PopulasiController::class, 'print'])->name('populasi.print');

        Route::resource('/monitoring', \App\Http\Controllers\MonitoringAyamController::class);
        Route::get('/monitoring/create', [MonitoringAyamController::class, 'create'])->name('monitoring.create');
        Route::post('/monitoring/store', [MonitoringAyamController::class, 'store'])->name('monitoring.store');

    });
    Route::prefix('pakan')->as('pakan.')->group(function () {

        Route::resource('/monitoringpakan', \App\Http\Controllers\MonitoringPakanController::class);
        Route::get('/monitoringpakan/create', [MonitoringPakanController::class, 'create'])->name('monitoringpakan.create');
        Route::post('/monitoringpakan/store', [MonitoringPakanController::class, 'store'])->name('monitoringpakan.store');
        Route::get('/pakan/monitoringpakan/print', [\App\Http\Controllers\MonitoringPakanController::class, 'print'])->name('monitoringpakan.print');

        Route::resource('/pakanmasuk', \App\Http\Controllers\PakanMasukController::class);
        Route::get('/pakanmasuk/create', [PakanMasukController::class, 'create'])->name('pakanmasuk.create');
        Route::post('/pakanmasuk/store', [PakanMasukController::class, 'store'])->name('pakanmasuk.store');
        Route::resource('/stokpakan', \App\Http\Controllers\MonitoringPakanDetailController::class);
        Route::resource('/pakankeluar', \App\Http\Controllers\PakanKeluarController::class);
        Route::get('/pakankeluar/create', [PakanKeluarController::class, 'create'])->name('pakankeluar.create');
        Route::post('/pakankeluar/store', [PakanKeluarController::class, 'store'])->name('pakankeluar.store');
        Route::delete('/pakankeluar/{id}', [PakanKeluarController::class, 'destroy'])->name('pakankeluar.destroy');
        Route::get('/pakankeluar/getStokPakan/{ayam_id}/{pakan_id}', [PakanKeluarController::class, 'getStokPakan'])
        ->name('pakankeluar.getStokPakan');
        Route::resource('/transferpakan', \App\Http\Controllers\PakanTransferController::class);
        Route::get('/transferpakan/create', [PakanTransferController::class, 'create'])->name('transferpakan.create');
        Route::post('/transferpakan/store', [PakanTransferController::class, 'store'])->name('transferpakan.store');
        Route::resource('obat', \App\Http\Controllers\ObatController::class)->except(['show', 'create', 'edit']);

    });
    Route::prefix('performa')->as('performa.')->group(function () {
        Route::resource('/ip', \App\Http\Controllers\PerformaController::class);
        Route::post('/ip/hitung-ip/{kandangId}', [PerformaController::class, 'hitungIP'])->name('ip.hitungIP');
    });

    // Rute transaksi barang
    Route::prefix('transaksi')->as('transaksi.')->group(function () {
        Route::get('/barangmasuk', [BarangmasukController::class, 'index'])->name('barangmasuk.index');
        Route::get('/barangkeluar', [BarangkeluarController::class, 'index'])->name('barangkeluar.index');
        Route::get('/barangkeluar/{id_keluar}', [BarangkeluarController::class, 'show'])->name('barangkeluar.show');
        Route::get('/barangkeluar/{id_keluar}/print', [BarangkeluarController::class, 'print'])->name('barangkeluar.print');
        Route::get('/detailbarangmasuk', [DetailbarangmasukController::class, 'index'])->name('detailbarangmasuk.index');
        Route::get('/detailbarangkeluar', [DetailbarangkeluarController::class, 'index'])->name('detailbarangkeluar.index');
        Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
        Route::get('/permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
        Route::post('/permintaan/store', [PermintaanController::class, 'store'])->name('permintaan.store');
        Route::get('/permintaan/{id_permintaan}', [PermintaanController::class, 'show'])->name('permintaan.show');
        Route::get('/permintaan/{id_permintaan}/print', [PermintaanController::class, 'print'])->name('permintaan.print');
        Route::put('/permintaan/{id_permintaan}', [PermintaanController::class, 'update'])->name('permintaan.update');
        Route::post('/permintaan/{id_permintaan}/approve', [PermintaanController::class, 'approveRequest'])->name('permintaan.approve');

        Route::get('/get-bagians', [PermintaanController::class, 'getBagians'])->name('getBagians');
        Route::get('/get-kategoris', [PermintaanController::class, 'getKategoris'])->name('getKategoris');

        Route::get('/barangmasuk', [BarangmasukController::class, 'index'])->name('barangmasuk.index');
        Route::get('/barangmasuk/create', [BarangmasukController::class, 'create'])->name('barangmasuk.create');
        Route::post('/barangmasuk/store', [BarangmasukController::class, 'store'])->name('barangmasuk.store');
        Route::get('/barangmasuk/{id_masuk}', [BarangmasukController::class, 'show'])->name('barangmasuk.show');
    });

    // Gaji
    Route::prefix('gaji')->as('gaji.')->group(function () {
        Route::resource('operasional', \App\Http\Controllers\OperasionalController::class)->except(['show', 'create', 'edit']);
        Route::resource('pinjaman', \App\Http\Controllers\PinjamanController::class)->except(['show', 'create', 'edit']);
        
        Route::get('/penggajian', [PerhitunganGajiController::class, 'index'])->name('penggajian.index');
        Route::get('/penggajian/create', [PerhitunganGajiController::class, 'create'])->name('penggajian.create');
        Route::post('/penggajian/store', [PerhitunganGajiController::class, 'store'])->name('penggajian.store');
        Route::get('/penggajian/{id_perhitungan}', [PerhitunganGajiController::class, 'show'])->name('penggajian.show');
        // Route::get('/penggajian/print/{id}', [App\Http\Controllers\PerhitunganGajiController::class, 'print'])->name('penggajian.print');
        Route::get('/gaji/print/{id_perhitungan}', [PerhitunganGajiController::class, 'print'])->name('penggajian.print');
        Route::get('/gaji/penggajian/print-slip/{id}', [PerhitunganGajiController::class, 'printSlip']) ->name('penggajian.print.slip');
        
    });
    // Sistem
    Route::prefix('sistem')->as('sistem.')->group(function () {
        // Route::get('/barangmasuk', [BarangmasukController::class, 'index'])->name('barangmasuk.index');
        // Route::get('/barangkeluar', [BarangkeluarController::class, 'index'])->name('barangkeluar.index');
        // Route::get('/barangkeluar/{id_keluar}', [BarangkeluarController::class, 'show'])->name('barangkeluar.show');
        // Route::get('/barangkeluar/{id_keluar}/print', [BarangkeluarController::class, 'print'])->name('barangkeluar.print');
        // Route::get('/detailbarangmasuk', [DetailbarangmasukController::class, 'index'])->name('detailbarangmasuk.index');
        // Route::get('/detailbarangkeluar', [DetailbarangkeluarController::class, 'index'])->name('detailbarangkeluar.index');
        Route::get('/masuk', [AyamController::class, 'index'])->name('masuk.index');
        Route::get('/masuk/create', [AyamController::class, 'create'])->name('masuk.create');
        Route::post('/masuk/store', [AyamController::class, 'store'])->name('masuk.store');
        Route::put('/masuk/{id_ayam}', [AyamController::class, 'update'])->name('masuk.update');
        Route::delete('/masuk/{id_ayam}', [AyamController::class, 'destroy'])->name('masuk.destroy');
        

        Route::resource('keluar', \App\Http\Controllers\AyammatiController::class)->except(['show', 'create', 'edit']);
        Route::get('/get-harga-ayam/{id}', [PanenController::class, 'getHargaAyam']); // Pindah di atas dulu
        Route::get('/panen', [PanenController::class, 'index'])->name('panen.index');
        Route::get('/panen/create', [PanenController::class, 'create'])->name('panen.create');
        Route::post('/panen/store', [PanenController::class, 'store'])->name('panen.store');
        Route::get('/panen/{id_panen}', [PanenController::class, 'show'])->name('panen.show');
        Route::get('/panen/{id_panen}/edit', [PanenController::class, 'edit'])->name('panen.edit');
        Route::put('panen/{id_panen}', [PanenController::class, 'update'])->name('panen.update');
        // Route::get('/get-harga-ayam/{id}', [PanenController::class, 'getHargaAyam']);


        // Route::get('/permintaan/{id_permintaan}', [PermintaanController::class, 'show'])->name('permintaan.show');
        // Route::get('/permintaan/{id_permintaan}/print', [PermintaanController::class, 'print'])->name('permintaan.print');
        // Route::put('/permintaan/{id_permintaan}', [PermintaanController::class, 'update'])->name('permintaan.update');
        // Route::post('/permintaan/{id_permintaan}/approve', [PermintaanController::class, 'approveRequest'])->name('permintaan.approve');

        // Route::get('/get-bagians', [PermintaanController::class, 'getBagians'])->name('getBagians');
        // Route::get('/get-kategoris', [PermintaanController::class, 'getKategoris'])->name('getKategoris');

        // Route::get('/barangmasuk', [BarangmasukController::class, 'index'])->name('barangmasuk.index');
        // Route::get('/barangmasuk/create', [BarangmasukController::class, 'create'])->name('barangmasuk.create');
        // Route::post('/barangmasuk/store', [BarangmasukController::class, 'store'])->name('barangmasuk.store');
        // Route::get('/barangmasuk/{id_masuk}', [BarangmasukController::class, 'show'])->name('barangmasuk.show');
    });

    Route::prefix('create')->as('create.')->group(function () {
        Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
        Route::get('/permission/create', [PermissionController::class, 'create'])->name('permission.create');
        Route::post('/permission/store', [PermissionController::class, 'store'])->name('permission.store');
        Route::get('/permission/{id_permission}', [PermissionController::class, 'show'])->name('permission.show');
        Route::get('/permission/{id_permission}/print', [PermissionController::class, 'print'])->name('permission.print');
        Route::get('/permission/{id_permission}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
        Route::put('permission/{id_permission}', [PermissionController::class, 'update'])->name('permission.update');
        Route::delete('/permission/{id_permission}', [PermissionController::class, 'destroy'])->name('permission.destroy');

        Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
        Route::get('/cuti/create', [CutiController::class, 'create'])->name('cuti.create');
        Route::post('/cuti/store', [CutiController::class, 'store'])->name('cuti.store');
    });

    Route::prefix('agenda')->as('agenda.')->group(function () {
        Route::get('incoming', [\App\Http\Controllers\IncomingLetterController::class, 'agenda'])->name('incoming');
        Route::get('incoming/print', [\App\Http\Controllers\IncomingLetterController::class, 'print'])->name('incoming.print');
        Route::get('outgoing', [\App\Http\Controllers\OutgoingLetterController::class, 'agenda'])->name('outgoing');
        Route::get('outgoing/print', [\App\Http\Controllers\OutgoingLetterController::class, 'print'])->name('outgoing.print');
    });

    Route::prefix('gallery')->as('gallery.')->group(function () {
        Route::get('incoming', [\App\Http\Controllers\LetterGalleryController::class, 'incoming'])->name('incoming');
        Route::get('outgoing', [\App\Http\Controllers\LetterGalleryController::class, 'outgoing'])->name('outgoing');
    });

    Route::prefix('reference')->as('reference.')->middleware(['role:admin'])->group(function () {
        Route::resource('classification', \App\Http\Controllers\ClassificationController::class)->except(['show', 'create', 'edit']);
        Route::resource('status', \App\Http\Controllers\LetterStatusController::class)->except(['show', 'create', 'edit']);
    });

    Route::prefix('lainnya')->as('lainnya.')->group(function () {
        Route::resource('suplier', \App\Http\Controllers\SuplierController::class)->except(['show', 'create', 'edit']);
        Route::resource('cabang', \App\Http\Controllers\CabangController::class)->except(['show', 'create', 'edit']);
        Route::resource('surat', \App\Http\Controllers\SuratController::class)->except(['show', 'create', 'edit']);
        Route::resource('setnomor', \App\Http\Controllers\SetnomorController::class)->except(['show', 'create', 'edit']);
        Route::resource('bagian', \App\Http\Controllers\BagianController::class)->except(['show', 'create', 'edit']);
        Route::resource('satuan', \App\Http\Controllers\SatuanController::class)->except(['show', 'create', 'edit']);
        Route::resource('ppn', \App\Http\Controllers\KategoribmController::class)->except(['show', 'create', 'edit']);
        Route::resource('suplierr', \App\Http\Controllers\SuplierrController::class)->except(['show', 'create', 'edit']);
        Route::resource('kandang', \App\Http\Controllers\KandangController::class)->except(['show', 'create', 'edit']);
        Route::resource('pakan', \App\Http\Controllers\PakanController::class)->except(['show', 'create', 'edit']);
        Route::resource('sekat', \App\Http\Controllers\SekatController::class)->except(['show', 'create', 'edit']);
        Route::resource('abk', \App\Http\Controllers\AbkController::class)->except(['show', 'create', 'edit']);
        Route::resource('doc', \App\Http\Controllers\HargadocController::class)->except(['show', 'create', 'edit']);
        Route::resource('hargaayam', \App\Http\Controllers\HargaAyamController::class)->except(['show', 'create', 'edit']);


        // Hitung tarif
        Route::get('tarif', [TarifAirController::class, 'index'])->name('tarif-air.index');
        Route::post('/tarif-air/hitung', [TarifAirController::class, 'hitung'])->name('tarif.hitung');
    });

    // Laporan
    Route::prefix('laporan')->as('laporan.')->middleware(['role:admin,staff,kasubagumumgudang'])->group(function () {
        Route::resource('rekap', \App\Http\Controllers\LaporanController::class)->except(['show', 'create', 'edit']);
        Route::get('print', [\App\Http\Controllers\LaporanController::class, 'print'])->name('print');
        Route::get('/laporan/debug', [LaporanController::class, 'debugLaporan'])->name('laporan.debug');
    });
});
