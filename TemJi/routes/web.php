<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeRedirectController::class)->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Tamu: registrasi warga (Farhan) dll.
Route::middleware('guest')->group(function () {
    foreach (glob(base_path('routes/modules/guest/*.php')) as $file) {
        require $file;
    }
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    foreach (glob(base_path('routes/modules/admin/*.php')) as $file) {
        require $file;
    }
});

Route::middleware(['auth', 'role:warga'])->prefix('warga')->name('warga.')->group(function () {
    Route::view('/dashboard', 'warga.dashboard')->name('dashboard');
    foreach (glob(base_path('routes/modules/warga/*.php')) as $file) {
        require $file;
    }
});

Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::view('/dashboard', 'petugas.dashboard')->name('dashboard');
    foreach (glob(base_path('routes/modules/petugas/*.php')) as $file) {
        require $file;
    }
});
