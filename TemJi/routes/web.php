<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IuranController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/iuran', [IuranController::class, 'index'])->name('admin.iuran.index');
    Route::post('/iuran', [IuranController::class, 'store'])->name('admin.iuran.store');
});