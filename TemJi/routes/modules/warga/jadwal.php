<?php

use App\Http\Controllers\Warga\JadwalWargaController;
use Illuminate\Support\Facades\Route;

// Grup /warga, nama 'warga.' -> warga.jadwal.index / warga.penjemputan-tambahan.store / .batal
Route::get('/jadwal', [JadwalWargaController::class, 'index'])->name('jadwal.index');
Route::post('/penjemputan-tambahan', [JadwalWargaController::class, 'store'])->name('penjemputan-tambahan.store');
Route::patch('/penjemputan-tambahan/{id}/batal', [JadwalWargaController::class, 'batal'])->name('penjemputan-tambahan.batal');
