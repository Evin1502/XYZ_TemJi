<?php

use App\Http\Controllers\Admin\AturanPenukaranPoinController;
use Illuminate\Support\Facades\Route;

Route::prefix('aturan-penukaran-poin')->name('aturan-penukaran.')->group(function () {
    Route::get('/', [AturanPenukaranPoinController::class, 'index'])->name('index');
    Route::post('/', [AturanPenukaranPoinController::class, 'store'])->name('store');
});
