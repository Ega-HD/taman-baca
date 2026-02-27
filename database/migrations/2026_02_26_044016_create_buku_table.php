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
        Schema::create('buku', function (Blueprint $table) {
            $table->id(); // Ini akan jadi acuan id_buku pengelompokan
            $table->string('judul_buku');
            $table->string('penulis'); // Wajib isi
            $table->string('penerbit'); // Wajib isi
            $table->integer('tahun_terbit'); // Wajib isi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
