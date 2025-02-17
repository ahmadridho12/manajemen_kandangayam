<?php
namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan
use App\Models\Permission;
use App\Models\Unit;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data surat izin terbaru terlebih dahulu dan melakukan pencarian berdasarkan 'unit_kerja'
        $data = Permission::where('unit_kerja', 'like', '%'.$request->search.'%')
                            ->orderBy('created_at', 'desc') // Mengurutkan berdasarkan tanggal terbaru
                            ->paginate(10);

        return view('pages.create.permission.index', [
            'data' => $data,
            'search' => $request->search,
        ]);
    }

    
    public function create(): View
    {
        $units = \App\Models\Unit::all(); // Mengambil semua data dari tabel unit
        $jabatans = \App\Models\Jabatan::all(); // Mengambil semua data dari tabel unit

        // Menampilkan form untuk membuat surat izin baru
        return view('pages.create.permission.add', [
            'units' => $units,
            'jabatans' => $jabatans, // Mengirim data unit ke view
        ]);    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'nama' => 'required',
            'nik' => 'required',
            'golongan' => 'required',
            'unit_kerja' => 'required',
            'nama_jabatan' => 'required',
            'tgl_buat' => 'required|date',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date',
            'perihal' => 'required',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Foto tidak wajib
        ]);
        
        $permission = new Permission();
        $permission->nama = $request->input('nama');
        $permission->nik = $request->input('nik');
        $permission->golongan = $request->input('golongan');
        $permission->unit_kerja = $request->input('unit_kerja');
        $permission->nama_jabatan = $request->input('nama_jabatan');
        $permission->tgl_buat = $request->input('tgl_buat');
        $permission->tgl_mulai = $request->input('tgl_mulai');
        $permission->tgl_selesai = $request->input('tgl_selesai');
        $permission->perihal = $request->input('perihal');
        // Buat objek Permission baru
        $permission = new Permission($request->except('foto')); // Ambil semua input kecuali foto
    
        // Jika file foto di-upload, proses penyimpanan
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('photos', 'public');
            $permission->foto = $fotoPath;
        } else {
            // Jika tidak ada file yang di-upload, set kolom foto sebagai null atau nilai default
            $permission->foto = null;
        }
    
        // Simpan data ke database
        $permission->save();
    
        return redirect()->route('create.permission.index'); // Redirect setelah penyimpanan
    }
    
    
    public function show($id_permission): View
    {
           // Mengambil permission dengan relasi unit
           $permission = Permission::with('unit')->findOrFail($id_permission);
           // Mengambil permission berdasarkan id_permission
        $permission = Permission::findOrFail($id_permission);
        return view('pages.create.permission.show', compact('permission'));
    }


    public function print($id_permission): View
    {
        // Mengambil data permission berdasarkan ID
        // Mengambil data permission beserta relasinya (pegawai dan jabatan) berdasarkan ID
        $permissions = Permission::with('jabatan.pegawai')->findOrFail($id_permission);
    
        // Mengambil judul berdasarkan bahasa yang sedang digunakan
        $title = app()->getLocale() == 'id' ? __('menu.permission.menu') . ' ' . __('menu.permission.permission') : __('menu.permission.permission') . ' ' . __('menu.permission.menu');
    
        // Mengirimkan data permission dan konfigurasi ke view
        return view('pages.create.permission.print', [
            'permissions' => $permissions, // Change this line
            'jabatans' => $permissions->jabatan, // Kirimkan variabel permission ke view
            'title' => $title,
            'nama' => $permissions->nama,
            'nik' => $permissions->nik,
            'golongan' => $permissions->golongan,
            'nama_jabatan' => $permissions->nama_jabatan,
            'unit_kerja' => $permissions->unit_kerja,
            'tgl_mulai' => $permissions->tgl_mulai,
            'tgl_buat' => $permissions->tgl_buat,
            'tgl_selesai' => $permissions->tgl_selesai,
            'perihal' => $permissions->perihal,
        ]);
    }

public function edit($id_permission): View
{
    // Mengambil semua data dari tabel unit
    $units = \App\Models\Unit::all(); 
    
    // Mengambil semua data dari tabel jabatan
    $jabatans = \App\Models\Jabatan::all(); 
    
    // Mengambil permission berdasarkan id_permission
    $permission = Permission::findOrFail($id_permission);
    
    // Memperbaiki compact dengan menutup tanda kutip dengan benar
    return view('pages.create.permission.edit', compact('permission', 'units', 'jabatans'));
}


public function update(Request $request, $id_permission): RedirectResponse
{
    // Validasi input
    $request->validate([
        'nama' => 'required',
        'nik' => 'required',
        'golongan' => 'required',
        'unit_kerja' => 'required',
        'nama_jabatan' => 'required',
        'tgl_buat' => 'required|date',
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date',
        'perihal' => 'required',
        'foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Foto tidak wajib
    ]);

    // Temukan permission berdasarkan ID
    $permission = Permission::findOrFail($id_permission);

    $permission->nama = $request->nama;
    $permission->nik = $request->nik;
    $permission->golongan = $request->golongan;
    $permission->tgl_buat = $request->tgl_buat;
    $permission->tgl_mulai = $request->tgl_mulai;
    $permission->tgl_selesai = $request->tgl_selesai;
    $permission->perihal = $request->perihal;

    //ambil unit
    $unit = Unit::findOrFail($request->unit_kerja);
    $permission->unit_kerja = $unit->unit_kerja;
    //ambil jabatan
    $jabatan = Jabatan::findOrFail($request->nama_jabatan);
    $permission->nama_jabatan = $jabatan->nama_jabatan;
    // Jika file foto di-upload, proses penyimpanan
    if ($request->hasFile('foto')) {
        $fotoPath = $request->file('foto')->store('photos', 'public');
        $permission->foto = $fotoPath;
    }

    // Simpan data ke database
    $permission->save();

    return redirect()->route('create.permission.index'); // Redirect setelah penyimpanan
}

public function destroy($id_permission): RedirectResponse
{
    $permission = Permission::find($id_permission);
    
    // Jika kategori tidak ditemukan, bisa tambahkan logika untuk menanganinya
    if (!$permission) {
        return redirect()->route('create.permission.index')->with('error', 'surat not found');
    }

    $permission->delete();
    return redirect()->route('create.permission.index')->with('success', 'Surat Berhasil Terhapus');
}
}