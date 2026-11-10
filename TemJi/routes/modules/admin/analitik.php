<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Modul Rute Analitik Admin (PBI 16 & PBI 17)
|--------------------------------------------------------------------------
| Dikelola secara mandiri sesuai panduan Runbook Merge PBI 1
| untuk mencegah konflik saat merge dengan cabang tim lain.
*/

// PBI 16: Dasbor Analitik & Visualisasi Grafik Tren
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// PBI 17: Ekspor Laporan CSV
Route::get('/admin/dashboard/export', [DashboardController::class, 'export'])->name('admin.dashboard.export');
