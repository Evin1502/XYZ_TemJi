<?php

use App\Http\Controllers\Warga\PoinWargaController;
use Illuminate\Support\Facades\Route;

// Rute Poin Warga: /warga/poin -> nama: warga.poin.index
Route::get('/poin', [PoinWargaController::class, 'index'])->name('poin.index');
