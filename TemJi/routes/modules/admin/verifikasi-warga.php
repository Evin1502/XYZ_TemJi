<?php

use App\Http\Controllers\Admin\VerifikasiWargaController;
use Illuminate\Support\Facades\Route;

Route::prefix('verifikasi-warga')->name('verifikasi-warga.')->group(function () {
    Route::get('/', [VerifikasiWargaController::class, 'index'])->name('index');
    Route::get('/{id}', [VerifikasiWargaController::class, 'show'])->name('show');
    Route::patch('/{id}/approve', [VerifikasiWargaController::class, 'approve'])->name('approve');
    Route::patch('/{id}/reject', [VerifikasiWargaController::class, 'reject'])->name('reject');
});
