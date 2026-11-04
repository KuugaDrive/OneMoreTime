<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Pencatatan Komisi & Operasional Agen JNE
|--------------------------------------------------------------------------
*/

// Autentikasi (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
});

// Logout (Auth Only)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Seluruh Rute Aplikasi Terproteksi Login & Status Aktif (Level 1)
Route::middleware(['auth', 'active'])->group(function () {
    // Redirect root sesuai role
    Route::get('/', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('transaksi.packing-list');
        }
        return redirect()->route('dashboard');
    });

    // Transaksi Packing List (Akses: Owner & Admin)
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/packing-list', [TransaksiController::class, 'packingList'])->name('packing-list');
        Route::post('/packing-list', [TransaksiController::class, 'storePackingList'])->name('packing-list.store');
        Route::put('/packing-list/{kode}', [TransaksiController::class, 'updatePackingList'])->name('packing-list.update');
        Route::delete('/packing-list/{kode}', [TransaksiController::class, 'destroyPackingList'])->name('packing-list.destroy');
    });

    // =========================================================================
    // KHUSUS OWNER (Role Middleware: role:owner)
    // =========================================================================
    Route::middleware('role:owner')->group(function () {
        // Dashboard Utama (Khusus Owner)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data CRUD
        Route::prefix('master')->name('master.')->group(function () {
            // User
            Route::get('/user', [MasterController::class, 'user'])->name('user');
            Route::post('/user', [MasterController::class, 'storeUser'])->name('user.store');
            Route::put('/user/{id}', [MasterController::class, 'updateUser'])->name('user.update');
            Route::delete('/user/{id}', [MasterController::class, 'destroyUser'])->name('user.destroy');

            // Cabang
            Route::get('/cabang', [MasterController::class, 'cabang'])->name('cabang');
            Route::post('/cabang', [MasterController::class, 'storeCabang'])->name('cabang.store');
            Route::put('/cabang/{id}', [MasterController::class, 'updateCabang'])->name('cabang.update');
            Route::delete('/cabang/{id}', [MasterController::class, 'destroyCabang'])->name('cabang.destroy');

            // Ops
            Route::get('/ops', [MasterController::class, 'ops'])->name('ops');
            Route::post('/ops', [MasterController::class, 'storeOps'])->name('ops.store');
            Route::put('/ops/{id}', [MasterController::class, 'updateOps'])->name('ops.update');
            Route::delete('/ops/{id}', [MasterController::class, 'destroyOps'])->name('ops.destroy');

            // Status
            Route::get('/status', [MasterController::class, 'status'])->name('status');
            Route::post('/status', [MasterController::class, 'storeStatus'])->name('status.store');
            Route::put('/status/{id}', [MasterController::class, 'updateStatus'])->name('status.update');
            Route::delete('/status/{id}', [MasterController::class, 'destroyStatus'])->name('status.destroy');
        });

        // Transaksi Operasional & Komisi Bulanan
        Route::prefix('transaksi')->name('transaksi.')->group(function () {
            Route::get('/operasional', [TransaksiController::class, 'operasional'])->name('operasional');
            Route::post('/operasional', [TransaksiController::class, 'storeOperasional'])->name('operasional.store');
            Route::put('/operasional/{kode}', [TransaksiController::class, 'updateOperasional'])->name('operasional.update');
            Route::delete('/operasional/{kode}', [TransaksiController::class, 'destroyOperasional'])->name('operasional.destroy');

            Route::get('/komisi', [TransaksiController::class, 'komisi'])->name('komisi');
            Route::post('/komisi', [TransaksiController::class, 'storeKomisi'])->name('komisi.store');
            Route::put('/komisi/{kode}', [TransaksiController::class, 'updateKomisi'])->name('komisi.update');
            Route::delete('/komisi/{kode}', [TransaksiController::class, 'destroyKomisi'])->name('komisi.destroy');
        });

        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/packing-list', [LaporanController::class, 'packingList'])->name('packing-list');
            Route::get('/packing-list/pdf', [LaporanController::class, 'packingListPdf'])->name('packing-list.pdf');

            Route::get('/operasional', [LaporanController::class, 'operasional'])->name('operasional');
            Route::get('/operasional/pdf', [LaporanController::class, 'operasionalPdf'])->name('operasional.pdf');

            Route::get('/laba-bersih', [LaporanController::class, 'labaBersih'])->name('laba-bersih');
            Route::get('/laba-bersih/pdf', [LaporanController::class, 'labaBersihPdf'])->name('laba-bersih.pdf');

            Route::get('/komisi', [LaporanController::class, 'komisi'])->name('komisi');
            Route::get('/komisi/pdf', [LaporanController::class, 'komisiPdf'])->name('komisi.pdf');
        });
    });
});
