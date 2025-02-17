<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sekat;
use App\Models\Monitoring;
use App\Models\Kandang;
use Illuminate\Support\Carbon;
class Ayam extends Model
{
    use HasFactory;

    protected $table = 'ayam';
    protected $primaryKey = 'id_ayam'; // Menentukan primary key
    protected $fillable = [
        'periode', 
        'tanggal_masuk', 
        'tanggal_selesai', 
        'qty_ayam', // Menambahkan kolom qty
        'status', // Menambahkan kolom status
        'kandang_id', // Menambahkan kolom kandang_id
    ];


    // public function sekat()
    // {
    //     return $this->belongsTo(Sekat::class, 'kandang_id', 'kandang_id'); // Relasi ke model Sekat berdasarkan kandang_id
    // }
    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'id_kandang'); // Relasi ke model Sekat berdasarkan kandang_id
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'ayam_id', 'id_ayam'); // Relasi ke model Monitoring
    }

    public function panen()
    {
        return $this->hasMany(Panen::class, 'ayam_id', 'id_ayam'); // Relasi ke model Panen
    }

    public function ayamMati()
    {
        return $this->hasMany(AyamMati::class, 'ayam_id', 'id_ayam'); // Relasi ke model AyamMati
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