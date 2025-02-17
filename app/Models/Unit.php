<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $table = 'unit'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key tabel
    protected $fillable = [
        'unit_kerja',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'unit_kerja', 'unit_kerja'); // Menggunakan 'id' sebagai primary key
    }
}
