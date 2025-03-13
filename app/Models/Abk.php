<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abk extends Model
{
    use HasFactory;

    protected $table = 'abk';
    protected $primaryKey = 'id_abk';
    public $timestamps = true; // Agar Laravel mengelola created_at dan updated_at secara otomatis

    protected $fillable = [
        'nama',
        'kandang_id',
        'jabatan',
        'status',
        'created_at'
    ];

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'id_kandang');
    }

}
