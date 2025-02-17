<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use HasFactory;

    protected $table = "satuan";

    protected $primaryKey = "id_satuan";

    protected $fillable = [
        "id_satuan",
        "nama_satuan",
    ];

    public function barangg()
    {
        return $this->hasMany(Barangg::class, 'id_satuan', 'id_satuan');
    }
}
