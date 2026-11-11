<?php

use App\Http\Controllers\Warga\IuranController;
use Illuminate\Support\Facades\Route;

Route::get('/iuran', [IuranController::class, 'index'])->name('iuran.index');
Route::post('/iuran/{id}/bayar', [IuranController::class, 'bayar'])->name('iuran.bayar');
