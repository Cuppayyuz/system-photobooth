<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // File: xxxx_xx_xx_xxxxxx_create_sesi_foto_table.php
    public function up(): void
    {
        Schema::create('sesi_foto', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->string('id_folder_gdrive')->nullable(); // ID Folder dari Google API
            $table->text('tautan_gdrive')->nullable();      // Link lengkap untuk QR Code
            $table->enum('status_cetak', ['menunggu', 'proses', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_foto');
    }
};
