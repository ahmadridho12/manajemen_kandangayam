<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Barangmasuk extends Model
{
    use HasFactory;
    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_masuk';

    protected $fillable = ['suplier_id', 'no_transaksi', 'tgl_masuk', 'keterangan'];

    public function suplier()
{
    return $this->belongsTo(Suplierr::class, 'suplier_id', 'id_suplier');
}


public function detail()
{
    return $this->hasMany(Detailbarangmasuk::class, 'barang_masuk_id', 'id_masuk'); 
    // Menyatakan hubungan dengan foreign key yang benar
}
// Scope untuk mendapatkan barang masuk hari ini
public function scopeToday($query)
{
    return $query->whereDate('tgl_masuk', Carbon::today());
}

// Scope untuk mendapatkan barang masuk kemarin
public function scopeYesterday($query)
{
    return $query->whereDate('tgl_masuk', Carbon::yesterday());
}


}
