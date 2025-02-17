<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekat extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'sekat';
    protected $primaryKey = 'id_sekat'; // Menentukan primary key
    protected $fillable = ['kandang_id', 'nama_sekat'];

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'id_kandang');
    }

    public function ayams()
    {
        return $this->hasMany(Ayam::class, 'sekat_id', 'id_sekat');
    }
}
