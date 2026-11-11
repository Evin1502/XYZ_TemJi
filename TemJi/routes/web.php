<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Route login sederhana agar tidak memicu ReflectionException
Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::post('/logout', function () {
    return redirect('/');
})->name('logout')->middleware('auth');

// Tamu
Route::middleware('guest')->group(function () {
    foreach (glob(base_path('routes/modules/guest/*.php')) as $file) {
        require $file;
    }
});

// Admin: Memuat otomatis route iuran dari routes/modules/admin/iuran.php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    foreach (glob(base_path('routes/modules/admin/*.php')) as $file) {
        require $file;
    }
});

// Warga
Route::middleware(['auth', 'role:warga'])->prefix('warga')->name('warga.')->group(function () {
    foreach (glob(base_path('routes/modules/warga/*.php')) as $file) {
        require $file;
    }
});

// Petugas
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    foreach (glob(base_path('routes/modules/petugas/*.php')) as $file) {
        require $file;
    }
});