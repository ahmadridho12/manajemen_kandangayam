<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sekat;
use App\Models\Ayam;
class Kandang extends Model
{
    use HasFactory;

    protected $table = 'kandang';
    protected $primaryKey = 'id_kandang'; // Menentukan primary key
    protected $fillable = ['nama_kandang', 'tanggal_mulai', 'tanggal_selesai', 'jumlah_skat'];

    // public function sekats()
    // {
    //     return $this->hasMany(Sekat::class, 'id_kandang', 'kandang_id');
    // }
    // Di model Kandang
    public function ayam()
    {
        return $this->hasMany(Ayam::class, 'kandang_id', 'id_kandang');
    }


}
