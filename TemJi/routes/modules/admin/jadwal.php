<?php

use App\Http\Controllers\Admin\JadwalPenjemputanController;
use Illuminate\Support\Facades\Route;

// admin.jadwal.index / admin.jadwal.store
Route::prefix('jadwal')->name('jadwal.')->group(function () {
    Route::get('/', [JadwalPenjemputanController::class, 'index'])->name('index');
    Route::post('/', [JadwalPenjemputanController::class, 'store'])->name('store');
});
