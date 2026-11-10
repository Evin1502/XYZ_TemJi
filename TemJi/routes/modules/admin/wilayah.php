<?php

use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

// Nama route: admin.wilayah.index / create / store / edit / update
Route::resource('wilayah', WilayahController::class)->except(['show', 'destroy']);
