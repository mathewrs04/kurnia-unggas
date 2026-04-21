<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BatchPembelianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\HargaAyamController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\MortalitasAyamController;
use App\Http\Controllers\BiayaOperasionalController;
use App\Http\Controllers\MetodePembayaranController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PeternakController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\LaporanKeuntunganController;
use App\Http\Controllers\SusutBatchController;
use App\Http\Controllers\TimbanganController;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::post('/login', [LoginController::class, 'handleLogin'])->name('login');

Route::middleware('auth')->group(function () {
    // Route yang bisa diakses semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('get-data')->as('get-data.')->group(function () {
        Route::get('peternak', [PeternakController::class, 'getData'])->name('peternak');
        Route::get('batch-pembelian', [PembelianController::class, 'getBatchPembelian'])->name('batch-pembelian');
        Route::get('delivery-order', [PembelianController::class, 'getDeliveryOrder'])->name('delivery-order');
    });

    // Route untuk Pemilik Usaha dan Penanggung Jawab
    Route::middleware(['role:pemilik,penanggung_jawab'])->group(function () {
        Route::get('/penjualan/laporan/harian', [PenjualanController::class, 'laporanHarian'])->name('penjualan.laporan-harian');
        Route::prefix('forecast')->as('forecast.')->controller(ForecastController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/train', 'train')->name('train');
            Route::post('/generate', 'generate')->name('generate');
            Route::get('/data', 'data')->name('data');
            Route::get('/evaluate', 'evaluate')->name('evaluate');
            Route::get('/rekomendasi', 'rekomendasi')->name('rekomendasi');
        });
    });

    // Route untuk Penanggung Jawab (akses semua)
    Route::middleware(['role:penanggung_jawab'])->group(function () {
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
            Route::prefix('karyawan')->as('karyawan.')->controller(KaryawanController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            });
            Route::prefix('timbangan')->as('timbangan.')->controller(TimbanganController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            });

            Route::prefix('batch-pembelian')->as('batch-pembelian.')->controller(BatchPembelianController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            });

            Route::prefix('pelanggan')->as('pelanggan.')->controller(PelangganController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            });

            Route::prefix('produk')->as('produk.')->controller(ProdukController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            });

            Route::prefix('metode-pembayaran')->as('metode-pembayaran.')->controller(MetodePembayaranController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            });

            Route::prefix('holiday')->as('holiday.')->controller(HolidayController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            });

            Route::prefix('harga-ayam')->as('harga-ayam.')->controller(HargaAyamController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            });
        });

        Route::prefix('delivery-order')->as('delivery-order.')->controller(DeliveryOrderController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });

        Route::prefix('pembelian')->as('pembelian.')->controller(PembelianController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::put('/{id}/link-do', 'linkDO')->name('link-do');
            Route::put('/{id}/bayar', 'bayar')->name('bayar');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });

        Route::prefix('biaya-operasional')->as('biaya-operasional.')->controller(BiayaOperasionalController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });

        Route::prefix('stok-opname')->as('stok-opname.')->controller(StokOpnameController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });

        Route::prefix('mortalitas-ayam')->as('mortalitas-ayam.')->controller(MortalitasAyamController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });

        Route::get('/susut-batch', [SusutBatchController::class, 'index'])->name('susut-batch.index');

        Route::prefix('report')->as('report.')->group(function () {
            Route::prefix('timbangan')->as('timbangan.')->controller(TimbanganController::class)->group(function () {
                Route::get('/', 'laporan')->name('index');
            });
            Route::prefix('keuntungan')->as('keuntungan.')->controller(LaporanKeuntunganController::class)->group(function () {
                Route::get('/', 'index')->name('index');
            });
            Route::prefix('setoran')->as('setoran.')->controller(SetoranController::class)->group(function () {
                Route::get('/', 'report')->name('index');
            });
        });

        Route::prefix('setoran')->as('setoran.')->controller(SetoranController::class)->group(function () {
            Route::put('/{id}/approve', 'approve')->name('approve');
        });
    });

    // Route untuk Kasir dan Penanggung Jawab (Penjualan)
    Route::middleware(['role:kasir,penanggung_jawab'])->group(function () {
        Route::prefix('setoran')->as('setoran.')->controller(SetoranController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
        });

        Route::prefix('penjualan')->as('penjualan.')->controller(PenjualanController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/show', 'show')->name('show');
            Route::put('/{id}/kirim', 'kirim')->name('kirim');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
            Route::get('/produk/{id}', 'getProduk')->name('get-produk');
            Route::get('/batch/{id}', 'getBatch')->name('get-batch');
        });
    });
});
