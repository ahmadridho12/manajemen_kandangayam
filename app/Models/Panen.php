<?php

namespace App\Models;
use App\Models\Ayam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Panen extends Model
{
    use HasFactory;

    protected $table = 'panen';
    protected $primaryKey = 'id_panen'; // Menentukan primary key
    protected $fillable = ['ayam_id', 'tanggal_panen', 'quantity', 'berat_total', 'atas_nama', 'no_panen', 'foto'];

    public function ayam()
{
    return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
}
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
