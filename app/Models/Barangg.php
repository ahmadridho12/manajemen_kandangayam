<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;


class Barangg extends Model // Ubah nama kelas ke Barangg
{
    use HasFactory;

    protected $table = 'barangg';

    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'kode_barang', // Ubah ke nama kolom yang benar
        'deskripsi', 
        'id_jenis', // Pastikan kolom ini ada di tabel barangg
        'id_satuan'
    ];

    // Relasi ke tabel `stok`
    public function stok()
    {
        return $this->hasMany(Stok::class, 'id_barang', 'id_barang');
    }

    // Relasi ke tabel `jenis`
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis', 'id'); // Pastikan ini merujuk ke id di tabel jenis
    }

    // Relasi ke tabel `satuan`
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
     // Fungsi untuk menghasilkan kode barang baru
 // Fungsi untuk menghasilkan kode barang baru
  // Fungsi untuk menghasilkan kode barang
  public function generateKodeBarang($id_jenis)
{
    // Ambil kode jenis dari tabel jenis
    $jenis = Jenis::find($id_jenis);

    if (!$jenis) {
        throw new \Exception('Jenis tidak ditemukan');
    }

    // Ambil kode terakhir dari barang yang sesuai dengan jenis ini
    $lastBarang = Barangg::where('id_jenis', $id_jenis)
        ->orderBy('kode_barang', 'desc')
        ->first();

    // Jika ada barang terakhir, ambil urutan setelah titik
    $lastKode = $lastBarang ? intval(explode('.', $lastBarang->kode_barang)[1]) : 0;

    // Generate kode barang baru
    $newKode = $jenis->kode . '.' . ($lastKode + 1); // Format: kode jenis + urutan

    return $newKode;
}

}
