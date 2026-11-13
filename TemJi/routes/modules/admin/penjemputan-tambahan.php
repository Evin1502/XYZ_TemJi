<?php

use App\Http\Controllers\Admin\PenjemputanTambahanController;
use Illuminate\Support\Facades\Route;

// admin.penjemputan-tambahan.index / .setujui / .tolak
Route::prefix('penjemputan-tambahan')->name('penjemputan-tambahan.')->group(function () {
    Route::get('/', [PenjemputanTambahanController::class, 'index'])->name('index');
    Route::patch('/{id}/setujui', [PenjemputanTambahanController::class, 'setujui'])->name('setujui');
    Route::patch('/{id}/tolak', [PenjemputanTambahanController::class, 'tolak'])->name('tolak');
});
