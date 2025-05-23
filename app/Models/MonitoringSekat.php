<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringSekat extends Model
{
    use HasFactory;

    protected $table = 'monitoring_sekat'; // Nama tabel jika tidak plural

    protected $primaryKey = 'id'; // Atau bisa dibiarkan default jika id

    protected $fillable = [
        'monitoring_id',
        'sekat_id',
        'body_weight',
        'daily_gain',
    ];

    // Relasi ke sekat
    public function sekat()
    {
        return $this->belongsTo(Sekat::class, 'sekat_id', 'id_sekat');
    }

    // Relasi ke monitoring_ayam
    public function monitoring()
    {
        return $this->belongsTo(MonitoringAyam::class, 'monitoring_id', 'id');
    }
}
