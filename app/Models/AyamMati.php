<?php

namespace App\Models;
use App\Models\Ayam;
use App\Models\Populasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AyamMati extends Model
{
    use HasFactory;

    protected $table = 'ayam_mati';
    protected $primaryKey = 'id_ayam_mati'; // Menentukan primary key
    protected $fillable = ['ayam_id', 'tanggal_mati', 'alasan', 'quantity_mati'];

    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }

    /**
     * Perbarui jumlah kematian di tabel populasi berdasarkan ayam_id dan tanggal_mati.
     */
    // public function populasi()
    // {
    //     return $this->belongsTo(Populasi::class, 'ayam_id', 'id_ayam');
    // }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    // Scope untuk mendapatkan barang keluar kemarin
    public function scopeYesterday($query)
    {
        return $query->whereDate('created_at', Carbon::yesterday());
    }
}
