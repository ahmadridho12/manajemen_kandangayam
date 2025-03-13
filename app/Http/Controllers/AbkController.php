<?php

namespace App\Http\Controllers;
use App\Models\Abk;
use App\Models\Kandang;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
class AbkController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Membuat query dasar
        $query = Abk::query();
    
        // Jika ada parameter pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
            // Ganti 'nama_kategori' dengan nama kolom yang sesuai di tabel Anda
        }
    
        // Menggunakan paginate untuk mendapatkan instance Paginator
        $data = $query->paginate(10); // 10 item per halaman
        $kandangs = Kandang::all(); // Ambil semua data Kandang

    
        return view('pages.lainnya.abk', [
            'data' => $data,
            'search' => $search,
            'kandangs' => $kandangs,

        ]);
    }
    // public function index()
    // {
    //     $kandang = Kandang::all();
    //     return view('kandang.index', compact('kandang'));
    // }

    // Menampilkan form untuk menambah kandang
   

    // Menyimpan data kandang baru
    public function store(Request $request)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id_kandang', // Pastikan ID kandang valid
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'status' => 'nullable|in:active,nonactive', // Validasi status

        ]);

        abk::create([
            'kandang_id' => $request->kandang_id,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'status' => $request->status ?? 'active', // Default 'active'
        ]);
        return redirect()->route('lainnya.abk.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit kandang
    public function edit(Abk $abk)
    {
        $kandangs = Kandang::all();
        return view('abk.edit', compact('abk', 'kandangs'));
    }

    // Mengupdate data kandang
    public function update(Request $request, Abk $abk)
{
    $request->validate([
        'kandang_id' => 'required|exists:kandang,id_kandang',
        'nama' => 'required|string|max:255',
        'jabatan' => 'required|string|max:255',
        'status' => 'nullable|in:active,nonactive', // Validasi status
    ]);

    $abk->update([
        'kandang_id' => $request->kandang_id,
        'nama' => $request->nama,
        'jabatan' => $request->jabatan,
        'status' => $request->status ?? $abk->status, // Jika status tidak diubah, tetap gunakan yang lama
    ]);

    return redirect()->route('lainnya.abk.index')->with('success', 'Petugas berhasil diperbarui.');
}


    // Menghapus kandang
    // public function destroy($id_abk)
    // {
        
    //     $abk = Abk::findOrFail($id_abk);
    //     $abk->delete();
        
    //     return redirect()->route('lainnya.abk.index')->with('success', 'Petugas berhasil dihapus.');
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Abk $abk): RedirectResponse
    {
        try {
            $abk->delete();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
