<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BatchPembelianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PeternakController;
use App\Http\Controllers\TimbanganController;
use App\Models\Pemasok;
use App\Models\Peternak;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::post('/login', [LoginController::class, 'handleLogin'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('get-data')->as('get-data.')->group(function () {
        Route::get('peternak', [PeternakController::class, 'getData'])->name('peternak');
        Route::get('batch-pembelian', [PembelianController::class, 'getBatchPembelian'])->name('batch-pembelian');
        Route::get('delivery-order', [PembelianController::class, 'getDeliveryOrder'])->name('delivery-order');
    });

    Route::prefix('master')->as('master.')->group(function () {
        Route::prefix('pemasok')->as('pemasok.')->controller(PemasokController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });
        Route::prefix('peternak')->as('peternak.')->controller(PeternakController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });
        Route::prefix('timbangan')->as('timbangan.')->controller(TimbanganController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });
        Route::prefix('delivery-order')->as('delivery-order.')->controller(DeliveryOrderController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });
        Route::prefix('batch-pembelian')->as('batch-pembelian.')->controller(BatchPembelianController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });
    });

    Route::prefix('pembelian')->as('pembelian.')->controller(PembelianController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/show', 'show')->name('show');
        Route::put('/{id}/bayar', 'bayar')->name('bayar');
        Route::delete('/{id}/destroy', 'destroy')->name('destroy');
    });
});
