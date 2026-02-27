<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'transaksi_peminjaman';

    protected $fillable = [
        'user_id',
        'item_buku_id',
        'tgl_pinjam',
        'deadline',
        'tgl_kembali',
        'hari_telat',
        'total_denda',
        'status',
    ];

    // Relasi ke User: Transaksi ini milik 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Buku: Transaksi ini meminjam 1 buku spesifik
    public function itemBuku()
    {
        return $this->belongsTo(ItemBuku::class);
    }
}