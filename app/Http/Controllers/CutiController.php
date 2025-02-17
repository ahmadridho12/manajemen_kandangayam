<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse; // Pastikan ini ditambahkan
use App\Models\Cuti;
use App\Models\Unit;
use Illuminate\Http\Request;


class CutiController extends Controller
{
    //
    public function index(Request $request)
    {
        // Mengambil semua data dari model 'Cuti'
        $data = Cuti::all();
        
        return view('pages.create.cuti.index', [
            'data' => $data,
        ]);
    }
    

    public function create(): View
    {
        $jabatans = \App\Models\Jabatan::all();
        $units = \App\Models\Unit::all(); // Mengambil semua data dari tabel unit
        return view('pages.create.cuti.add', [
            'units' => $units,
            'jabatans' => $jabatans, // Mengirim data unit ke view
        ]);
    }

    // public function store(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'nama' => 'required',
    //         'nik' => 'required',
    //         'golongan' => 'required',
    //         'unit_kerja' => 'required',
    //         'jabatan' => 'required',
    //         'tgl_buat' => 'required|date',
    //         'mulai_cuti' => 'required|date',
    //         'sampai_cuti' => 'required|date',
    //         'nama_kuasa' => 'required',
    //         'nik_kuasa' => 'required',
    //         'jabatan' => 'required',
    //         'nohp_kuasa' => 'required',
    //     ]);
    // }

}
