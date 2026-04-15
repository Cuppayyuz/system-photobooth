<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemFoto extends Model
{
    // 1. Kasih tau Laravel nama tabel aslinya
    protected $table = 'item_foto';

    // 2. Izinkan Mass Assignment untuk kolom-kolom ini
    protected $fillable = [
        'sesi_foto_id', 
        'jalur_foto_asli', 
        'jalur_foto_frame'
    ];

    // 3. Relasi: Item Foto ini milik 1 Sesi Foto
    public function sesiFoto(): BelongsTo
    {
        return $this->belongsTo(SesiFoto::class, 'sesi_foto_id');
    }
}