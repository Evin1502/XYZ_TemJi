<?php

use App\Http\Controllers\Admin\JadwalPenjemputanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin/jadwal')->name('admin.jadwal.')->group(function () {
    Route::get('/', [JadwalPenjemputanController::class, 'index'])->name('index');
    Route::post('/', [JadwalPenjemputanController::class, 'store'])->name('store');
});
