<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TarifAir;

class TarifAirController extends Controller
{
    public function index()
    {
        $types = [
            'IN' => 'INDUSTRI',
            'IP' => 'INSTANSI PEMERINTAH',
            'KH' => 'KH',
            'NB' => 'NIAGA BESAR',
            'NK' => 'NIAGA KECIL',
            'RM' => 'RUMAH MENENGAH',
            'RS' => 'RUMAH SEDERHANA',
            'RW' => 'RUMAH MEWAH',
            'SK' => 'SOSIAL KHUSUS',
            'SU' => 'SOSIAL UMUM'
        ];

        return view('pages.lainnya.tarif', compact('types'));
    }

    public function hitung(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:IN,IP,KH,NB,NK,RM,RS,RW,SK,SU',
            'kubik' => 'required|numeric|min:0'
        ]);

        try {
            $hasil = TarifAir::hitungTagihan($validatedData['type'], $validatedData['kubik']);

            $types = [
                'IN' => 'INDUSTRI',
                'IP' => 'INSTANSI PEMERINTAH',
                'KH' => 'KH',
                'NB' => 'NIAGA BESAR',
                'NK' => 'NIAGA KECIL',
                'RM' => 'RUMAH MENENGAH',
                'RS' => 'RUMAH SEDERHANA',
                'RW' => 'RUMAH MEWAH',
                'SK' => 'SOSIAL KHUSUS',
                'SU' => 'SOSIAL UMUM'
            ];

            return view('pages.lainnya.tarif', [
                'types' => $types,
                'hasil' => $hasil,
                'type' => $validatedData['type'],
                'kubik' => $validatedData['kubik']
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}