<?php

use App\Http\Controllers\PengajuanDaurUlangController;
use Illuminate\Support\Facades\Route;

// Grup /warga, nama 'warga.' -> warga.daur-ulang.create / store / jadwal
Route::get('/daur-ulang', [PengajuanDaurUlangController::class, 'create'])->name('daur-ulang.create');
Route::post('/daur-ulang', [PengajuanDaurUlangController::class, 'store'])->name('daur-ulang.store');
Route::get('/daur-ulang/{kode}/jadwal', [PengajuanDaurUlangController::class, 'pilihJadwal'])->name('daur-ulang.jadwal');
