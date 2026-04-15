<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiFoto extends Model
{
    // 1. Kasih tau Laravel nama tabel aslinya
    protected $table = 'sesi_foto';

    // 2. Izinkan Mass Assignment untuk kolom-kolom ini
    protected $fillable = [
        'nama_pelanggan', 
        'id_folder_gdrive', 
        'tautan_gdrive', 
        'status_cetak'
    ];

    // 3. Relasi: 1 Sesi Foto punya Banyak Item Foto
    public function itemFoto(): HasMany
    {
        return $this->hasMany(ItemFoto::class, 'sesi_foto_id');
    }
}