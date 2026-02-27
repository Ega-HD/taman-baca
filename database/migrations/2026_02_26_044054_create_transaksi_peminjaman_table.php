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
            
            $table->date('tgl_pinjam');
            $table->date('deadline');
            $table->date('tgl_kembali')->nullable(); // Nullable karena saat dipinjam belum ada tgl kembali
            $table->integer('hari_telat')->default(0);
            $table->integer('total_denda')->default(0);
            $table->enum('status', ['Sedang Dipinjam', 'Selesai'])->default('Sedang Dipinjam');
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
