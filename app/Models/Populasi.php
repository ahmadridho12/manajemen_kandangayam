<?php

namespace App\Models;
use App\Models\AyamMati;
use App\Services\PopulasiGeneratorService;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Populasi extends Model
{
    use HasFactory;

    protected $table = 'populasi';
    protected $primaryKey = 'id_populasi';
    protected $fillable = [
        'tanggal',
        'populasi',  // ini FK ke id_ayam di tabel ayam
        'mati',
        'panen',
        'qty_mati',
        'qty_panen',
        'total',
        'qty_now',
        'day'
        
    ];

    // Relasi dengan tabel ayam
    public function ayam()
{
    return $this->belongsTo(Ayam::class, 'populasi', 'id_ayam');
}
    public function ayamMati()
    {
        return $this->belongsTo(AyamMati::class, 'mati', 'id_ayam_mati');
    }
    public function panens()
    {
        return $this->belongsToMany(Panen::class, 'populasi_panen', 'populasi_id', 'panen_id');
    }

    public static function regenerateForAyam($ayamId) {
        // Hapus data populasi yang ada
        self::where('id_ayam', $ayamId)->delete();
        
        // Generate ulang
        $generator = new PopulasiGeneratorService();
        $generator->generateFromAyam($ayamId);
    }
}