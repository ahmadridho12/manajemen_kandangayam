<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abk extends Model
{
    use HasFactory;

    protected $table = 'abk';
    protected $primaryKey = 'id_abk';
    protected $fillable = [
        'nama',
        'jabatan',
        'status',
        'craeted_at'
    ];
}
