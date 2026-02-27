<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBuku extends Model
{
    use HasFactory;

    protected $table = 'item_buku';

    protected $fillable = [
        'buku_id',
        'kode_buku', // Wajib diisi dan unik
        'status_buku',
        'asal_buku',
        'tgl_ditambahkan',
    ];

    // Relasi balik: Item buku ini adalah milik 1 Katalog Induk
    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    // Relasi ke transaksi: 1 fisik buku bisa punya banyak riwayat peminjaman
    public function transaksiPeminjaman()
    {
        return $this->hasMany(TransaksiPeminjaman::class);
    }
}
