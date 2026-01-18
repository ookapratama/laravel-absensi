<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\KantorController;
use App\Http\Controllers\JenisIzinController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\IzinController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard as home page
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // User CRUD routes
    Route::resource('user', UserController::class)->middleware('check.permission:user.index');

    // Role & Menu Management
    Route::resource('role', \App\Http\Controllers\RoleController::class)->middleware('check.permission:role.index');
    Route::resource('menu', \App\Http\Controllers\MenuController::class)->middleware('check.permission:menu.index');
    Route::get('permission', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permission.index')->middleware('check.permission:permission.index');
    Route::put('permission', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permission.update')->middleware('check.permission:permission.index');

    // Products CRUD routes
    Route::resource('products', ProductsController::class)->middleware('check.permission:products.index');

    // Activity Log
    Route::get('activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('activity-log/data', [\App\Http\Controllers\ActivityLogController::class, 'getData'])->name('activity-log.data');
    Route::get('activity-log/statistics', [\App\Http\Controllers\ActivityLogController::class, 'statistics'])->name('activity-log.statistics');

    // ============== DATA MASTER ==============
    // Divisi
    Route::resource('divisi', DivisiController::class)->middleware('check.permission:divisi.index');

    // Kantor
    Route::resource('kantor', KantorController::class)->middleware('check.permission:kantor.index');

    // Jenis Izin
    Route::resource('jenis-izin', JenisIzinController::class)->middleware('check.permission:jenis-izin.index');

    // Pegawai
    Route::resource('pegawai', PegawaiController::class)->middleware('check.permission:pegawai.index');

    // ============== ABSENSI ==============
    Route::prefix('absensi')->name('absensi.')->group(function () {
        // Halaman absensi untuk pegawai
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::post('/masuk', [AbsensiController::class, 'absenMasuk'])->name('masuk');
        Route::post('/pulang', [AbsensiController::class, 'absenPulang'])->name('pulang');
        Route::post('/validate-location', [AbsensiController::class, 'validateLocation'])->name('validate-location');
        Route::get('/history', [AbsensiController::class, 'history'])->name('history');

        // Dashboard & Rekap untuk Admin
        Route::get('/dashboard', [AbsensiController::class, 'dashboard'])->name('dashboard')->middleware('check.permission:absensi.dashboard');
        Route::get('/rekap', [AbsensiController::class, 'rekap'])->name('rekap')->middleware('check.permission:absensi.rekap');
    });

    // ============== IZIN ==============
    // Route untuk pegawai
    Route::prefix('izin')->name('izin.')->group(function () {
        Route::get('/', [IzinController::class, 'index'])->name('index');
        Route::get('/create', [IzinController::class, 'create'])->name('create');
        Route::post('/', [IzinController::class, 'store'])->name('store');
        Route::get('/{izin}', [IzinController::class, 'show'])->name('show');
        Route::delete('/{izin}/cancel', [IzinController::class, 'cancel'])->name('cancel');

        // Route untuk admin
        Route::prefix('admin')->name('admin.')->middleware('check.permission:izin.admin')->group(function () {
            Route::get('/', [IzinController::class, 'adminIndex'])->name('index');
            Route::get('/pending', [IzinController::class, 'pending'])->name('pending');
            Route::post('/{izin}/approve', [IzinController::class, 'approve'])->name('approve');
            Route::post('/{izin}/reject', [IzinController::class, 'reject'])->name('reject');
        });
    });
});

