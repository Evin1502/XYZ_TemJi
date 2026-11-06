<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman utama: jika terotentikasi alihkan ke dashboard peran, jika belum alihkan ke login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->getDefaultRedirectRoute());
    }

    return redirect()->route('login');
})->name('home');

// Rute Tamu (Guest): Otentikasi Login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Rute Logout (Memerlukan login)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rute Terproteksi Peran: Admin (Pengelola Pusat)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

// Master Data Wilayah (Hanya dapat dikelola oleh Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('wilayah', WilayahController::class)->except(['show', 'destroy']);
});

// Rute Terproteksi Peran: Petugas (Operasional Lapangan)
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', function () {
        return view('petugas.dashboard');
    })->name('dashboard');
});

// Rute Terproteksi Peran: Warga (Layanan Masyarakat)
Route::middleware(['auth', 'role:warga'])->prefix('warga')->name('warga.')->group(function () {
    Route::get('/dashboard', function () {
        return view('warga.dashboard');
    })->name('dashboard');
});
