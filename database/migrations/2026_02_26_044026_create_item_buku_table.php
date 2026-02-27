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
        Schema::create('item_buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade'); // Relasi ke katalog
            $table->string('kode_buku')->unique(); // Contoh: PAUD-001, PAUD-002
            $table->enum('status_buku', ['Tersedia', 'Di-booking', 'Dipinjam'])->default('Tersedia');
            $table->enum('asal_buku', ['Baru', 'Donasi']);
            $table->date('tgl_ditambahkan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_buku');
    }
};
