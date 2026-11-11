<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/analitik', [DashboardController::class, 'index'])->name('analitik');
Route::get('/analitik/export', [DashboardController::class, 'export'])->name('analitik.export');
