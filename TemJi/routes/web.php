<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IuranController as AdminIuranController;
use App\Http\Controllers\Warga\IuranController as WargaIuranController;

Route::get('/', function () {
    return view('welcome');
});

// Route Admin (PBI 13: Nominal & Terbit Tagihan)
Route::prefix('admin')->group(function () {
    Route::get('/iuran', [AdminIuranController::class, 'index'])->name('admin.iuran.index');
    Route::post('/iuran', [AdminIuranController::class, 'store'])->name('admin.iuran.store');
    Route::put('/iuran/{id}', [AdminIuranController::class, 'update'])->name('admin.iuran.update');
});

// Route Warga (PBI 14: Tagihan & Riwayat Pembayaran)
Route::prefix('warga')->group(function () {
    Route::get('/iuran', [WargaIuranController::class, 'index'])->name('warga.iuran.index');
    Route::post('/iuran/{id}/bayar', [WargaIuranController::class, 'bayar'])->name('warga.iuran.bayar');
});