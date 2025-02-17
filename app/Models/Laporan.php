<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Laporan extends Model
{
    use HasFactory;

    public static function ambilDataLaporan($search = null, $since = null, $until = null, $id_jenis = null)
    {
        // Enable query logging
        DB::enableQueryLog();

        // Nonaktifkan ONLY_FULL_GROUP_BY untuk sesi ini
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        // Konversi tanggal
        $startDate = $since 
            ? Carbon::createFromFormat('Y-m-d', $since)->startOfMonth() 
            : Carbon::now()->startOfMonth();
        
        $endDate = $until 
            ? Carbon::createFromFormat('Y-m-d', $until)->endOfMonth() 
            : Carbon::now()->endOfMonth();

        // Get previous month for saldo awal
        $previousMonth = clone $startDate;
        $previousMonth->subMonth();
        $previousEndDate = $previousMonth->endOfMonth()->format('Y-m-d H:i:s');

        // Log query parameters
        Log::info('Query Parameters', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'previousEndDate' => $previousEndDate,
            'search' => $search,
            'id_jenis' => $id_jenis
        ]);

        // Subquery untuk saldo awal
        $previousBalance = DB::table('detail_masuk as dm')
            ->select([
                'dm.id_detailmasuk',
                'dm.id_barang',
                'dm.harga_setelah_ppn',
                'dm.tanggal_detailmasuk',
                'dm.jumlah as qty_masuk_original',
                DB::raw("(
                    SELECT COALESCE(SUM(dbk.jumlah), 0)
                    FROM detail_barang_keluar dbk
                    WHERE dbk.id_barang = dm.id_barang
                    AND dbk.harga = dm.harga_setelah_ppn
                    AND dbk.tanggal_detailkeluar <= '{$previousEndDate}'
                    AND dbk.tanggal_detailkeluar >= dm.tanggal_detailmasuk
                ) as qty_keluar_before")
            ])
            ->where('dm.tanggal_detailmasuk', '<=', $previousEndDate)
            ->havingRaw('COALESCE(qty_masuk_original, 0) - COALESCE(qty_keluar_before, 0) > 0');

        // Subquery untuk transaksi masuk periode berjalan
        $currentIncoming = DB::table('detail_masuk')
            ->select([
                'id_detailmasuk',
                'id_barang',
                'harga_setelah_ppn',
                'tanggal_detailmasuk',
                'jumlah as qty_in'
            ])
            ->whereBetween('tanggal_detailmasuk', [$startDate, $endDate]);

        // Subquery untuk transaksi keluar dengan implementasi FIFO
        $currentOutgoing = DB::table('detail_barang_keluar as dbk')
            ->join('detail_masuk as dm', function ($join) {
                $join->on('dbk.id_barang', '=', 'dm.id_barang')
                     ->on('dbk.harga', '=', 'dm.harga_setelah_ppn')
                     ->whereRaw('dm.tanggal_detailmasuk <= dbk.tanggal_detailkeluar');
            })
            ->select([
                'dbk.id_barang',
                'dm.id_detailmasuk',
                'dm.harga_setelah_ppn',
                DB::raw('SUM(LEAST(dbk.jumlah, GREATEST(dm.jumlah - COALESCE((
                    SELECT SUM(dbk2.jumlah)
                    FROM detail_barang_keluar dbk2
                    WHERE dbk2.id_barang = dm.id_barang
                    AND dbk2.harga = dm.harga_setelah_ppn
                    AND dbk2.tanggal_detailkeluar < dbk.tanggal_detailkeluar
                ), 0), 0))) as qty_out')
            ])
            ->whereBetween('dbk.tanggal_detailkeluar', [$startDate, $endDate])
            ->groupBy('dbk.id_barang', 'dm.id_detailmasuk', 'dm.harga_setelah_ppn');

        // Query utama
        $mainQuery = DB::table('barangg as b')
            ->join('detail_masuk as dm', 'b.id_barang', '=', 'dm.id_barang')
            ->leftJoin('satuan as s', 'b.id_satuan', '=', 's.id_satuan')
            ->leftJoinSub($previousBalance, 'pb', function ($join) {
                $join->on('dm.id_detailmasuk', '=', 'pb.id_detailmasuk');
            })
            ->leftJoinSub($currentIncoming, 'ci', function ($join) {
                $join->on('dm.id_detailmasuk', '=', 'ci.id_detailmasuk');
            })
            ->leftJoinSub($currentOutgoing, 'co', function ($join) {
                $join->on('dm.id_detailmasuk', '=', 'co.id_detailmasuk');
            })
            ->select([
                'b.id_barang',
                'b.deskripsi',
                'b.kode_barang',
                's.nama_satuan',
                'dm.harga_setelah_ppn as harga',
                'dm.tanggal_detailmasuk',
                'dm.id_detailmasuk',
                DB::raw('COALESCE(pb.qty_masuk_original - pb.qty_keluar_before, 0) as qty_awal_periode'),
                DB::raw('COALESCE(ci.qty_in, 0) as qty_masuk'),
                DB::raw('COALESCE(co.qty_out, 0) as qty_keluar')
            ])
            ->when($search, function ($query) use ($search) {
                return $query->where('b.deskripsi', 'like', "%{$search}%");
            })
            ->when($id_jenis, function ($query) use ($id_jenis) {
                return $query->where('b.id_jenis', $id_jenis);
            })
            ->where(function ($query) use ($startDate, $endDate, $previousEndDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('dm.tanggal_detailmasuk', [$startDate, $endDate]);
                })
                ->orWhere(function ($q) use ($previousEndDate) {
                    $q->where('dm.tanggal_detailmasuk', '<=', $previousEndDate);
                });
            })
            ->orderBy('b.kode_barang')
            ->orderBy('dm.tanggal_detailmasuk')
            ->orderBy('dm.id_detailmasuk')
            ->distinct();

        // Final query dengan perhitungan nilai
        $result = DB::query()->fromSub(function ($subQuery) use ($mainQuery) {
            $subQuery->from(DB::raw("({$mainQuery->toSql()}) as md"))
                ->mergeBindings($mainQuery)
                ->select([
                    'id_barang',
                    'deskripsi',
                    'kode_barang',
                    'nama_satuan',
                    'harga',
                    'tanggal_detailmasuk',
                    'id_detailmasuk',
                    'qty_awal_periode',
                    DB::raw('qty_awal_periode * harga as saldo_awal'),
                    'qty_masuk',
                    DB::raw('qty_masuk * harga as nilai_masuk'),
                    'qty_keluar',
                    DB::raw('qty_keluar * harga as nilai_keluar'),
                    DB::raw('GREATEST(qty_awal_periode + qty_masuk - qty_keluar, 0) as qty_akhir')
                ]);
        }, 'final')
        ->select([
            'id_barang',
            'deskripsi',
            'kode_barang',
            'nama_satuan',
            'harga',
            'tanggal_detailmasuk',
            'id_detailmasuk',
            'qty_awal_periode',
            'saldo_awal',
            'qty_masuk',
            'nilai_masuk',
            'qty_keluar',
            'nilai_keluar',
            'qty_akhir',
            DB::raw('qty_akhir * harga as saldo_akhir')
        ])
        ->orderBy('kode_barang')
        ->orderBy('tanggal_detailmasuk')
        ->orderBy('id_detailmasuk');

        $allData = $result->get();

        $filteredData = collect($allData)->filter(function ($item) use ($startDate, $endDate) {
            $item = is_object($item) ? (array)$item : $item;
            
            $itemDate = Carbon::parse($item['tanggal_detailmasuk']);
            
            $hasCurrentTransaction = ($item['qty_masuk'] ?? 0) > 0 || ($item['qty_keluar'] ?? 0) > 0;
            
            $hasRemainingStock = ($item['qty_akhir'] ?? 0) > 0;
            
            return $hasCurrentTransaction || ($hasRemainingStock && $itemDate < $startDate);
        });

        // Log query yang dijalankan
        Log::info('Executed Queries', [
            'queries' => DB::getQueryLog()
        ]);

        // Reset kembali mode SQL
        DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode,',ONLY_FULL_GROUP_BY'));");

        // Pagination
        $page = request()->get('page', 1);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredData->values()->slice(($page - 1) * 25, 25)->toArray(),
            $filteredData->count(),
            25,
            $page,
            ['path' => request()->url()]
        );
    }

    // Metode tambahan untuk generate ringkasan laporan
    public static function generateReportSummary($data)
    {
        return [
            'total_barang' => $data->count(),
            'total_qty_awal' => $data->sum('qty_awal_periode'),
            'total_qty_masuk' => $data->sum('qty_masuk'),
            'total_qty_keluar' => $data->sum('qty_keluar'),
            'total_saldo_awal' => $data->sum('saldo_awal'),
            'total_saldo_akhir' => $data->sum('saldo_akhir')
        ];
    }
}