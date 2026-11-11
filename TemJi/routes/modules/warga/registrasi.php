<?php

use App\Http\Controllers\Auth\RegistrasiWargaController;
use Illuminate\Support\Facades\Route;

// Berada di grup /warga dengan nama 'warga.' -> warga.registrasi.status / warga.registrasi.resubmit
Route::get('/registrasi/status', [RegistrasiWargaController::class, 'statusPendaftaran'])->name('registrasi.status');
Route::post('/registrasi/{id}/resubmit', [RegistrasiWargaController::class, 'resubmit'])->name('registrasi.resubmit');
