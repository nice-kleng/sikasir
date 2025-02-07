<?php

use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::controller(AuthenticateController::class)->group(function () {
    Route::get('/', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('auth');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('/products', ProductController::class);

    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
});
