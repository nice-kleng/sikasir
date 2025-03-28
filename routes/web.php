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
    Route::get('/report/{id}', [App\Http\Controllers\ReportController::class, 'showItem'])->name('report.show');
    // Route::get('/report/{id}/print', [App\Http\Controllers\ReportController::class, 'printNota'])->name('report.print');
    // Route::get('/report/{id}/refund', [App\Http\Controllers\RefundController::class, 'create'])->name('report.refund');

    Route::get('/setting', [App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting', [App\Http\Controllers\SettingController::class, 'update'])->name('setting.update');

    Route::resource('/kategori', App\Http\Controllers\KategoriController::class);

    Route::get('/transactions/refund', [App\Http\Controllers\RefundController::class, 'index'])->name('refund.index');
    Route::get('/transactions/{transaction_id}/create', [App\Http\Controllers\RefundController::class, 'create'])->name('refund.create');
    Route::post('/transactions/refund', [App\Http\Controllers\RefundController::class, 'store'])->name('refund.store');
});
// Route::post('/transactions/refund', [KasirController::class, 'refund'])->name('refund');
Route::post('/midtrans/callback', [KasirController::class, 'handle'])->name('midtrans.callback');
