<?php

use App\Http\Controllers\Admin\KategoriDaurUlangController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KategoriDaurUlangController::class, 'index'])->name('home');

Route::prefix('admin/kategori-daur-ulang')->name('admin.kategori.')->group(function () {
    Route::get('/', [KategoriDaurUlangController::class, 'index'])->name('index');
    Route::post('/', [KategoriDaurUlangController::class, 'store'])->name('store');
    Route::put('/{id}', [KategoriDaurUlangController::class, 'update'])->name('update');
    Route::patch('/{id}/toggle-status', [KategoriDaurUlangController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{id}/jadwalkan-tarif', [KategoriDaurUlangController::class, 'jadwalkanTarif'])->name('jadwalkan-tarif');
});
