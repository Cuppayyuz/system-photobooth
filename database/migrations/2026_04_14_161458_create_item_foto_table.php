<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // File: xxxx_xx_xx_xxxxxx_create_item_foto_table.php
    public function up(): void
    {
        Schema::create('item_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_foto_id')->constrained('sesi_foto')->onDelete('cascade');
            $table->string('jalur_foto_asli');   // Path foto tanpa frame
            $table->string('jalur_foto_frame');  // Path foto yang sudah ada frame
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_foto');
    }
};
