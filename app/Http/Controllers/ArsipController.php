<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Arsip;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class ArsipController extends Controller
{
    //
    public function indexmou()
    {
        $arsip = Arsip::all();
        return view('Admin.Arsip.indexmou', compact('mou'));
    }

    public function show($id_ar)
    {
        $arsip = Arsip::where('id_arsip', $id_arsip)->first();
       // return view('Admin.Arsip.showmou', ['arsip' => $arsip]);
    }

    public function create()
    {
        return view('pages.user');
    }

    public function storearsip(Request $request)
    {
        $data = $request->all();
        date_default_timezone_set('Asia/Bangkok');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('mou_files', $fileName, 'public');

            $data['file'] = $fileName; // Simpan hanya nama file di database
        }

        

        Mou::create([
            'tgl_berlaku' => date('Y-m-d h:i:s'),
            'tgl_selesai' => date('Y-m-d h:i:s'),
            'tgl_ditetapkan' => date('Y-m-d h:i:s'),
            'judul' => $data['judul'],
            'nomor' => $data['nomor'],
            'status' => $data['status'],
            'instusi' => $data['instusi'],
            'file' => $data['file'],
        ]);

        return redirect()->route('indexmou');
    }

    public function edit($id_mou)
    {
        $mou = Mou::where('id_mou', $id_mou)->first();
    
        if (!$mou) {
            return redirect()->back()->with('notif', 'Mou tidak ditemukan');
        }
    
        // Pastikan tanggal diubah menjadi objek Carbon
        $mou->tgl_ditetapkan = Carbon::parse($mou->tgl_ditetapkan);
        $mou->tgl_berlaku = Carbon::parse($mou->tgl_berlaku);
        $mou->tgl_selesai = Carbon::parse($mou->tgl_selesai);
    
        return view('Admin.Arsip.editmou', ['mou' => $mou]);
    }

    public function update(Request $request, $id_mou)
{
    $mou = Mou::find($id_mou);

    if (!$mou) {
        return redirect()->back()->with('notif', 'Mou tidak ditemukan');
    }

    $update = [];

    if ($request->hasFile('file')) {
        // Hapus file lama jika ada
        if ($mou->file && Storage::disk('public')->exists('mou_files/' . $mou->file)) {
            Storage::disk('public')->delete('mou_files/' . $mou->file);
        }

        // Upload file baru
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('mou_files', $fileName, 'public');

        $update['file'] = $fileName; // Simpan nama file baru di database
    } else {
        // Jika tidak ada file baru, gunakan file lama
        $update['file'] = $mou->file;
    }

    // Update data lainnya
    $mou->update([
        'judul' => $request->input('judul'),
        'nomor' => $request->input('nomor'),
        'instusi' => $request->input('instusi'),
        'tgl_berlaku' => $request->input('tgl_berlaku'),
        'tgl_selesai' => $request->input('tgl_selesai'),
        'tgl_ditetapkan' => $request->input('tgl_ditetapkan'),
        'status' => $request->input('status'),
        'file' => $update['file'], // Update nama file di database
    ]);

    return redirect()->route('indexmou')->with('success', 'Data berhasil diperbarui');
}

    
    

    public function destroy($id_mou)
    {
        $mou = Mou::findOrFail($id_mou); 
        $mou->delete();
        return redirect()->route('indexmou');
    }

    public function downloadFile($file)
{
    $filePath = storage_path('app/public/mou_files/' . $file);

    if (!file_exists($filePath)) {
        abort(404);
    }

    // Mendapatkan ekstensi file
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

    // Menentukan MIME type berdasarkan ekstensi file
    $mimeType = mime_content_type($filePath);

    return response()->download($filePath, $file, ['Content-Type' => $mimeType]);
}
}

