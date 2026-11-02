<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Tambahkan ini

Route::get('/', function () {
    return view('welcome');
});

// Route untuk Dashboard Analitik PBI 16
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');