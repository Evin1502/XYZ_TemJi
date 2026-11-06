<?php

use App\Http\Controllers\PengajuanDaurUlangController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('pengajuan-daur-ulang.create');
});

// Fitur Pengajuan Daur Ulang Terpilah (XT-16 PBI 10)
Route::get('/pengajuan-daur-ulang', [PengajuanDaurUlangController::class, 'create'])
    ->name('pengajuan-daur-ulang.create');
Route::post('/pengajuan-daur-ulang', [PengajuanDaurUlangController::class, 'store'])
    ->name('pengajuan-daur-ulang.store');
Route::get('/pengajuan-daur-ulang/{kode}/jadwal', [PengajuanDaurUlangController::class, 'pilihJadwal'])
    ->name('pengajuan-daur-ulang.jadwal');

// Alias untuk navigasi warga
Route::get('/warga/daur-ulang', [PengajuanDaurUlangController::class, 'create'])
    ->name('warga.daur-ulang.create');
