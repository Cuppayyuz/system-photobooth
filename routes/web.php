<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoboothController;
use App\Models\SesiFoto; // Pastikan ini ada di atas

Route::get('/', function () {
    return view('welcome');
});
Route::get('/photobooth', function () {
    return view('photobooth');
});
// Rute untuk menerima foto dari Canvas JS
Route::post('/api/simpan-foto', [PhotoboothController::class, 'simpanFoto']);

Route::get('/monitor', function () {
    return view('monitor');
});

Route::get('/gallery/{kode_sesi}', function($kode_sesi) {
    return view('gallery', ['kode_sesi' => $kode_sesi]);
});