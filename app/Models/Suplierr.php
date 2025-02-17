<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suplierr extends Model
{
    use HasFactory;
    protected $table = 'suplierr';
    // protected $guarded = ['id_suplier'];
    protected $primaryKey = 'id_suplier';
    protected $fillable = ['nama_suplier', 'alamat', 'npwp', 'note'];
}
