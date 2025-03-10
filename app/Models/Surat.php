<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surat';

    // protected $primaryKey = 'nomasurat';

    protected $fillable = [
        'nosurat',
        'nomor',
        'jenis',
        'bulan',
        'tahun',
        ];
}
