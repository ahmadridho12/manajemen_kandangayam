<?php

namespace App\Services;

class TarifAir {
    private static $tarif = [
        'IN' => [
            'nama' => 'INDUSTRI',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 3700],
                ['min' => 11, 'max' => 20, 'tarif' => 3950],
                ['min' => 21, 'max' => 30, 'tarif' => 4300],
                ['min' => 31, 'max' => PHP_INT_MAX, 'tarif' => 4300],

                // ['min' => 31, 'max' => 40, 'tarif' => 0],
                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'IP' => [
            'nama' => 'INSTANSI PEMERINTAH',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 2100],
                ['min' => 11, 'max' => 20, 'tarif' => 2200],
                ['min' => 21, 'max' => 30, 'tarif' => 2400],
                ['min' => 31, 'max' => 40, 'tarif' => 2700],
                ['min' => 41, 'max' => PHP_INT_MAX, 'tarif' => 2700],

                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'KH' => [
            'nama' => 'KH',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 15300],
                ['min' => 11, 'max' => 20, 'tarif' => 15300],
                ['min' => 21, 'max' => 30, 'tarif' => 15300],
                ['min' => 31, 'max' => 40, 'tarif' => 15300],
                ['min' => 41, 'max' => PHP_INT_MAX, 'tarif' => 15300],

                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'NB' => [
            'nama' => 'NIAGA BESAR',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 3500],
                ['min' => 11, 'max' => 20, 'tarif' => 3750],
                ['min' => 21, 'max' => 30, 'tarif' => 4150],
                ['min' => 31, 'max' => PHP_INT_MAX, 'tarif' => 4150],

                // ['min' => 31, 'max' => 40, 'tarif' => 0],
                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'NK' => [
            'nama' => 'NIAGA KECIL',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 3100],
                ['min' => 11, 'max' => 20, 'tarif' => 3300],
                ['min' => 21, 'max' => 30, 'tarif' => 3650],
                ['min' => 31, 'max' => PHP_INT_MAX, 'tarif' => 3650],

                // ['min' => 31, 'max' => 40, 'tarif' => 0],
                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'RM' => [
            'nama' => 'RUMAH MENENGAH',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 2600],
                ['min' => 11, 'max' => 20, 'tarif' => 2750],
                ['min' => 21, 'max' => 30, 'tarif' => 3050],
                ['min' => 31, 'max' => 40, 'tarif' => 3550],
                ['min' => 41, 'max' => PHP_INT_MAX, 'tarif' => 3550],
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'RS' => [
            'nama' => 'RUMAH SEDERHANA',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 2000],
                ['min' => 11, 'max' => 20, 'tarif' => 2100],
                ['min' => 21, 'max' => 30, 'tarif' => 2300],
                ['min' => 31, 'max' => 40, 'tarif' => 2600],
                ['min' => 41, 'max' => PHP_INT_MAX, 'tarif' => 2600],

                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'RW' => [
            'nama' => 'RUMAH MEWAH',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 2950],
                ['min' => 11, 'max' => 20, 'tarif' => 3100],
                ['min' => 21, 'max' => 30, 'tarif' => 3400],
                ['min' => 31, 'max' => 40, 'tarif' => 3900],
                ['min' => 41, 'max' => PHP_INT_MAX, 'tarif' => 3900],

                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'SK' => [
            'nama' => 'SOSIAL KHUSUS',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 2000],
                ['min' => 11, 'max' => 20, 'tarif' => 2100],
                ['min' => 21, 'max' => 30, 'tarif' => 2300],
                ['min' => 31, 'max' => 40, 'tarif' => 2600],
                ['min' => 41, 'max' => PHP_INT_MAX, 'tarif' => 2600],

                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ],
        'SU' => [
            'nama' => 'SOSIAL UMUM',
            'rentang' => [
                ['min' => 0, 'max' => 10, 'tarif' => 1000],
                ['min' => 11, 'max' => 20, 'tarif' => 1100],
                ['min' => 21, 'max' => 30, 'tarif' => 1300],
                ['min' => 31, 'max' => 40, 'tarif' => 1600],
                ['min' => 41, 'max' => PHP_INT_MAX, 'tarif' => 1600],

                // ['min' => 41, 'max' => 50, 'tarif' => 0]
            ],
            'biaya_tambahan' => [
                'pendaftaran' => 0,
                'jasa_adm' => 4000,
                'dana_meter' => 0,
                'pemeliharaan' => 4000,
                'sampah' => 0,
                'lainnya' => 0
            ]
        ]
    ];

    public static function hitungTagihan($kode, $kubik) {
        if (!isset(self::$tarif[$kode])) {
            throw new \Exception("Golongan tidak ditemukan");
        }
    
        $golongan = self::$tarif[$kode];
        $totalBiayaKubik = 0;
        $detailKubik = [];
        $sisaKubik = $kubik;
    
        // Proses perhitungan untuk setiap rentang
        foreach ($golongan['rentang'] as $rentang) {
            $batasBawah = $rentang['min'];
            $batasAtas = $rentang['max'];
            $tarif = $rentang['tarif'];
    
            // Tentukan jumlah kubik untuk rentang ini
            $jumlahKubikRentang = min($batasAtas - $batasBawah + 1, $sisaKubik);
    
            if ($jumlahKubikRentang > 0) {
                // Pastikan rentang 0-10 tepat 10 m³
                if ($batasBawah === 0 && $kubik > 10) {
                    $jumlahKubikRentang = 10;
                }
    
                // Khusus untuk rentang terakhir, gunakan sisa kubik
                if ($batasAtas === PHP_INT_MAX) {
                    $jumlahKubikRentang = $sisaKubik;
                }
    
                $subtotal = $jumlahKubikRentang * $tarif;
                $totalBiayaKubik += $subtotal;
    
                $detailKubik[] = [
                    'min' => $batasBawah,
                    'max' => $batasAtas === PHP_INT_MAX ? 'Seterusnya' : $batasAtas,
                    'tarif' => $tarif,
                    'jumlah_kubik' => $jumlahKubikRentang,
                    'subtotal' => $subtotal
                ];
    
                $sisaKubik -= $jumlahKubikRentang;
            }
    
            // Jika sudah tidak ada sisa kubik, hentikan perhitungan
            if ($sisaKubik <= 0) {
                break;
            }
        }
    
        // Hitung biaya tambahan
        $biayaTambahanTotal = array_sum($golongan['biaya_tambahan']);
        $totalTagihan = $totalBiayaKubik + $biayaTambahanTotal;
    
        return [
            'golongan' => $golongan['nama'],
            'detail_kubik' => $detailKubik,
            'biaya_kubik' => $totalBiayaKubik,
            'biaya_tambahan' => $golongan['biaya_tambahan'],
            'total_biaya_tambahan' => $biayaTambahanTotal,
            'total_tagihan' => $totalTagihan
        ];
    }
}    