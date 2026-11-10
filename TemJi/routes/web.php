<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Modul Rute Analitik Admin (PBI 16 & PBI 17 - Runbook Merge PBI 1)
if (file_exists(__DIR__ . '/modules/admin/analitik.php')) {
    require __DIR__ . '/modules/admin/analitik.php';
} else {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/export', [DashboardController::class, 'export'])->name('admin.dashboard.export');
}
