<?php

use App\Http\Controllers\Admin\IuranController;
use Illuminate\Support\Facades\Route;

Route::get('/iuran', [IuranController::class, 'index'])->name('iuran.index');
Route::post('/iuran', [IuranController::class, 'store'])->name('iuran.store');
Route::put('/iuran/{id}', [IuranController::class, 'update'])->name('iuran.update');