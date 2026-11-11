<?php

use App\Http\Controllers\Auth\RegistrasiWargaController;
use Illuminate\Support\Facades\Route;

Route::get('/registrasi-warga', [RegistrasiWargaController::class, 'showRegistrationForm'])->name('warga.registrasi.form');
Route::post('/registrasi-warga', [RegistrasiWargaController::class, 'register'])->name('warga.registrasi.store');
