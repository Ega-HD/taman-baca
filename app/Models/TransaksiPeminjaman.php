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

        'approved_by_id',
        'retrieved_by_id',
        'rejected_by_id',
        'updated_by_id',

        'tgl_pengajuan_pinjam',
        'tgl_disetujui',
        'tgl_ditolak',
        'tgl_diupdate',
        'tgl_pinjam',
        'deadline',
        'tgl_kembali',
        'tgl_pelunasan',

        'hari_telat',
        'tarif_denda_berlaku',
        'total_denda',
        'status',
    ];

    // Relasi ke User: Transaksi ini milik 1 user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi BARU untuk siapa yang menyetujui (Admin)
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    // Relasi ke Buku: Transaksi ini meminjam 1 buku spesifik
    public function itemBuku()
    {
        return $this->belongsTo(ItemBuku::class, 'item_buku_id', 'id');
    }

    public function retrievedBy()
    {
        return $this->belongsTo(User::class, 'retrieved_by_id');
    }

    public function rejectedBy() 
    { 
        return $this->belongsTo(User::class, 'rejected_by_id'); 
    }
    
    public function updatedBy() 
    { 
        return $this->belongsTo(User::class, 'updated_by_id'); 
    }
}