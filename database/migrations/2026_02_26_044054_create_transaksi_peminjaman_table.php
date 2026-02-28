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
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('item_buku_id')->constrained('item_buku')->onDelete('cascade');
            
            $table->dateTime('tgl_pengajuan')->useCurrent(); // Otomatis terisi saat request dibuat
            $table->dateTime('tgl_disetujui')->nullable(); // Kosong sampai di-ACC admin

            $table->date('tgl_pinjam')->nullable();
            $table->date('deadline')->nullable();
            $table->date('tgl_kembali')->nullable(); // Nullable karena saat dipinjam belum ada tgl kembali
            $table->integer('hari_telat')->default(0);
            $table->integer('total_denda')->default(0);
            $table->dateTime('tgl_pelunasan')->nullable();
            $table->enum('status', [
                'Menunggu Persetujuan',
                'Sedang Dipinjam', 
                'Menunggu Pengembalian', 
                'Dikembalikan'
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
