<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasi';

    protected $fillable = [
        'nama_donatur',
        'no_hp_donatur',
        'judul_buku',
        'jumlah_buku',
        'tgl_donasi',
        'keterangan',
    ];
}