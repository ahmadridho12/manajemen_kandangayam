<?php

namespace App\Models;
use App\Models\Ayam;
use App\Models\Kandang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat';
    protected $primaryKey = 'id_obat';
    protected $fillable = [
        'ayam_id',
        'kandang_id',
        'nama_obat',
        'total'
    ];
    public function ayam()
    {
        return $this->belongsTo(Ayam::class, 'ayam_id', 'id_ayam');
    }
    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'id_kandang');
    }
}
