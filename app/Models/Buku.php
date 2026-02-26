<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    // Beritahu Laravel nama tabel pastinya, agar tidak otomatis mencari tabel "bukus"
    protected $table = 'buku'; 

    protected $fillable = [
        'judul_buku',
        'tgl_ditambahkan',
        'status_buku',
        'asal_buku',
        'penulis',
        'penerbit',
        'tahun_terbit',
    ];

    // Relasi ke transaksi: 1 buku bisa ada di banyak riwayat transaksi
    public function transaksiPeminjaman()
    {
        return $this->hasMany(TransaksiPeminjaman::class);
    }
}