<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoboothController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/photobooth', function () {
    return view('photobooth');
});
// Rute untuk menerima foto dari Canvas JS
Route::post('/api/simpan-foto', [PhotoboothController::class, 'simpanFoto']);