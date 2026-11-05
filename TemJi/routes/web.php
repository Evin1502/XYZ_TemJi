<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WilayahController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('wilayah', WilayahController::class)->except(['show', 'destroy']);