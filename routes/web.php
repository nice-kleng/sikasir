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
    Route::get('/kasir/print-nota/{transaction_id}', [KasirController::class, 'printNota'])->name('kasir.print-nota');
    Route::get('/report', [App\Http\Controllers\ReportController::class, 'index'])->name('report.index');
    Route::get('/report/pdf', [App\Http\Controllers\ReportController::class, 'generatePDF'])->name('report.pdf');

    Route::get('/setting', [App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting', [App\Http\Controllers\SettingController::class, 'update'])->name('setting.update');

    Route::resource('/kategori', App\Http\Controllers\KategoriController::class);
});

Route::get('/refund', function () {
    return view('form-refund');
});
Route::post('/transactions/refund', [KasirController::class, 'refund'])->name('refund');
Route::post('/midtrans/callback', [KasirController::class, 'handle'])->name('midtrans.callback');
