<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notrans extends Model
{
    use HasFactory;

    protected $table = 'notrans';
    protected $fillable = ['penamaan', 'last_number'];

    public function permintaan()
    {
        return $this->hasMany(Permintaan::class, 'no_trans', 'id_notrans');
    }
}
