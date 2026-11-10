<?php

use App\Http\Controllers\Admin\AturanPenukaranPoinController;
use App\Http\Controllers\Admin\KategoriDaurUlangController;
use App\Http\Controllers\Admin\VerifikasiWargaController;
use App\Http\Controllers\Auth\RegistrasiWargaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KategoriDaurUlangController::class, 'index'])->name('home');

Route::prefix('admin/kategori-daur-ulang')->name('admin.kategori.')->group(function () {
    Route::get('/', [KategoriDaurUlangController::class, 'index'])->name('index');
    Route::post('/', [KategoriDaurUlangController::class, 'store'])->name('store');
    Route::put('/{id}', [KategoriDaurUlangController::class, 'update'])->name('update');
    Route::patch('/{id}/toggle-status', [KategoriDaurUlangController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{id}/jadwalkan-tarif', [KategoriDaurUlangController::class, 'jadwalkanTarif'])->name('jadwalkan-tarif');
});

Route::prefix('admin/aturan-penukaran-poin')->name('admin.aturan-penukaran.')->group(function () {
    Route::get('/', [AturanPenukaranPoinController::class, 'index'])->name('index');
    Route::post('/', [AturanPenukaranPoinController::class, 'store'])->name('store');
});

Route::prefix('registrasi-warga')->name('warga.registrasi.')->group(function () {
    Route::get('/', [RegistrasiWargaController::class, 'showRegistrationForm'])->name('form');
    Route::post('/', [RegistrasiWargaController::class, 'register'])->name('store');
    Route::get('/status', [RegistrasiWargaController::class, 'statusPendaftaran'])->name('status');
    Route::post('/{id}/resubmit', [RegistrasiWargaController::class, 'resubmit'])->name('resubmit');
});

Route::prefix('admin/verifikasi-warga')->name('admin.verifikasi-warga.')->group(function () {
    Route::get('/', [VerifikasiWargaController::class, 'index'])->name('index');
    Route::get('/{id}', [VerifikasiWargaController::class, 'show'])->name('show');
    Route::patch('/{id}/approve', [VerifikasiWargaController::class, 'approve'])->name('approve');
    Route::patch('/{id}/reject', [VerifikasiWargaController::class, 'reject'])->name('reject');
});
