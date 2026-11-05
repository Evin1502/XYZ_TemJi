<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\WargaController;

Route::get('/', fn () => redirect()->route('wilayah.index'));

Route::resource('wilayah', WilayahController::class)->except(['show', 'destroy']);

Route::post('warga/bulk-assign', [WargaController::class, 'bulkAssign'])->name('warga.bulk-assign');
Route::resource('warga', WargaController::class)->except(['show', 'destroy']);
