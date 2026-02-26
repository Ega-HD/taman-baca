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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Enum untuk hak akses
            $table->enum('role', ['super_admin', 'admin', 'pengunjung'])->default('pengunjung');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('nama_lengkap');
            $table->text('alamat')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('no_hp'); // Wajib diisi
            $table->string('email')->unique()->nullable(); // Opsional
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
