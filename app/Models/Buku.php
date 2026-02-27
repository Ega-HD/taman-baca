<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku'; 

    // Kolom ini wajib diisi dan tidak boleh kosong sesuai kebutuhanmu
    protected $fillable = [
        'judul_buku',
        'penulis',
        'penerbit',
        'tahun_terbit',
    ];

    // Relasi: 1 Katalog Buku memiliki Banyak Item (Fisik) Buku
    public function itemBuku()
    {
        return $this->hasMany(ItemBuku::class);
    }
}