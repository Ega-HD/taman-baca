<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_peminjaman', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users dan buku
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('item_buku_id')->constrained('item_buku')->onDelete('cascade');

            // Pencatatan Aktor
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('retrieved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');

            
            // Pencatatan Tanggal
            $table->dateTime('tgl_pengajuan_pinjam')->useCurrent(); // Otomatis terisi saat request dibuat
            $table->dateTime('tgl_disetujui')->nullable(); // Kosong sampai di-ACC admin
            
            $table->dateTime('tgl_ditolak')->nullable(); // Kosong sampai di-ACC admin
            $table->dateTime('tgl_diupdate')->nullable(); // Kosong sampai di-ACC admin

            $table->dateTime('tgl_pinjam')->nullable();
            $table->dateTime('deadline')->nullable();

            $table->dateTime('tgl_pengajuan_pengembalian')->nullable();
            $table->dateTime('tgl_kembali')->nullable(); // Nullable karena saat dipinjam belum ada tgl kembali
            $table->dateTime('tgl_pelunasan')->nullable();

            $table->integer('hari_telat')->default(0);
            $table->integer('tarif_denda_berlaku')->nullable();
            $table->integer('total_denda')->default(0);
            $table->enum('status', [
                'Menunggu Persetujuan',
                'Sedang Dipinjam', 
                'Menunggu Pengembalian', 
                'Dikembalikan',
                'Ditolak'
            ])->default('Menunggu Persetujuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_peminjaman');
    }
};
