<?php

use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

Route::post('warga/bulk-assign', [WargaController::class, 'bulkAssign'])->name('warga.bulk-assign');
Route::resource('warga', WargaController::class)->only(['index', 'edit', 'update']);
