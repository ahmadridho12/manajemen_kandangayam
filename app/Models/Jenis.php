<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;

    // protected $primaryKey = 'id'; // Set the primary key

    protected $table = 'jenis';

    protected $fillable = [
        'kode',
        'nama',
  
    ];
    public function stok()
    {
        return $this->hasMany(Stok::class, 'id_jenis', 'id');
    }
    public function barangg()
    {
        return $this->hasMany(Barangg::class, 'kode_barang', 'kode');
    }
    public function DetailBarangKeluar()
    {
        return $this->hasMany(DetailBarangKeluar::class, 'id_jenis', 'id');
    }

}
