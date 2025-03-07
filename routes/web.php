<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMasterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UmumController;
use Illuminate\Support\Facades\Auth;

// Kelompok Rute untuk Admin
Route::middleware(['auth','role:admin'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboardAdmin'])->name('dashboard');
    Route::get('/rangkuman', [AdminController::class, 'rangkuman'])->name('rangkuman');
    Route::get('/rangkuman/cetak', [AdminController::class, 'rangkumanCetak'])->name('rangkuman.cetak');
    Route::get('/tabel-ph', [AdminController::class, 'tabelPH'])->name('tabel.PH');
    Route::get('/tabel-tds', [AdminController::class, 'tabelTDS'])->name('tabel.TDS');
    Route::get('/tabel-udara', [AdminController::class, 'tabelUdara'])->name('tabel.udara');
    Route::get('/tabel-arus', [AdminController::class, 'tabelArus'])->name('tabel.arus');
    Route::get('/tabel-reservoir', [AdminController::class, 'tabelReservoir'])->name('tabel.reservoir');
    Route::get('/pengaturan-akun', [AdminController::class, 'pengaturanAkun'])->name('akun-admin.pengaturan');
});

// Kelompok Rute untuk Admin Master
Route::middleware(['auth', 'role:admin-master'])->prefix('/admin-master')->name('admin-master.')->group(function () {
    Route::get('/dashboard', [AdminMasterController::class, 'dashboardAdmin'])->name('dashboard');
    Route::get('/rangkuman', [AdminMasterController::class, 'rangkuman'])->name('rangkuman');
    Route::get('/rangkuman/cetak', [AdminMasterController::class, 'rangkumanCetak'])->name('rangkuman.cetak');
    Route::get('/tabel-ph', [AdminMasterController::class, 'tabelPH'])->name('tabel.PH');
    Route::get('/tabel-tds', [AdminMasterController::class, 'tabelTDS'])->name('tabel.TDS');
    Route::get('/tabel-udara', [AdminMasterController::class, 'tabelUdara'])->name('tabel.udara');
    Route::get('/tabel-arus', [AdminMasterController::class, 'tabelArus'])->name('tabel.arus');
    Route::get('/tabel-reservoir', [AdminMasterController::class, 'tabelReservoir'])->name('tabel.reservoir');
    Route::get('/pengaturan-akun', [AdminMasterController::class, 'pengaturanAkun'])->name('akun.pengaturan');
    Route::get('/daftar-admin', [AdminMasterController::class, 'daftarAdmin'])->name('akun.daftar-admin');
    Route::get('/daftar-admin/view/{id}', [AdminMasterController::class, 'viewAdmin'])->name('akun.daftar-admin.view');
});

Route::get('/check-session', function () {
    return response()->json([
        'auth_user' => Auth::user(),
        'session_id' => session()->getId(),
    ]);
})->name('check-session');

// Rute untuk logout
Route::middleware('auth')->prefix('/auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Rute untuk login
Route::middleware('guest')->prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'authLogin'])->name('login');
});

// Kelompok Rute untuk Umum
Route::middleware('guest')->prefix('/')->name('umum.')->group(function () {
    Route::get('/dashboard', [UmumController::class, 'dashboardUmum'])->name('dashboard');
    Route::get('/rangkuman', [UmumController::class, 'rangkuman'])->name('rangkuman');
    Route::get('/rangkuman/cetak', [UmumController::class, 'rangkumanCetak'])->name('rangkuman.cetak');
    Route::get('/tabel-ph', [UmumController::class, 'tabelPH'])->name('tabel.PH');
    Route::get('/tabel-tds', [UmumController::class, 'tabelTDS'])->name('tabel.TDS');
    Route::get('/tabel-udara', [UmumController::class, 'tabelUdara'])->name('tabel.udara');
    Route::get('/tabel-arus', [UmumController::class, 'tabelArus'])->name('tabel.arus');
    Route::get('/tabel-reservoir', [UmumController::class, 'tabelReservoir'])->name('tabel.reservoir');
});

Route::fallback(function () {
    return redirect()->route('umum.dashboard')->with('error', 'Halaman yang Anda cari tidak ditemukan.');
});
