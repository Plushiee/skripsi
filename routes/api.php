<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// API MQTT Start
Route::post('/send/mqtt', [ApiController::class, 'sendMqtt'])->name('api.send.mqtt');
// API MQTT End

// API GET Start
Route::post('/get/PH', [ApiController::class, 'getPH'])->name('api.get.PH');
Route::post('/get/TDS', [ApiController::class, 'getTDS'])->name('api.get.TDS');
Route::post('/get/udara', [ApiController::class, 'getUdara'])->name('api.get.udara');
Route::post('/get/arusair', [ApiController::class, 'getArusAir'])->name('api.get.arusAir');
Route::post('/get/ping', [ApiController::class, 'getPing'])->name('api.get.ping');
Route::post('/get/user', [ApiController::class, 'getUser'])->name('api.get.user');
Route::post('/get/admin/photo', [ApiController::class, 'getPhoto'])->name('api.get.admin.photo');
// API GET End

// API POST Start
Route::post('/post/pompa', [ApiController::class, 'postPompa'])->name('api.post.pompa');

Route::post('/post/admin', [ApiController::class, 'postUser'])->name('api.post.admin');
// API POST End

// API UPDATE Start (Admin)
Route::middleware('auth')->prefix('/update/admin')->group(function () {
    Route::post('/bio', [ApiController::class, 'updateAdmin'])->name('api.admin-utama.update.bio');
    Route::post('/photo', [ApiController::class, 'updateAdminPhoto'])->name('api.admin-utama.update.photo');
});
// API UPDATE End

// (Admin Utama Only)
Route::middleware(['auth', 'role:admin-master'])->prefix('/update/admin-master')->group(function () {
    // API UPDATE Start (Admin Utama Only)
    Route::post('/nama', [ApiController::class, 'updateAdminNama'])->name('api.admin-utama.update.nama');
    Route::post('/jam-kerja', [ApiController::class, 'updateJamKerja'])->name('api.admin-utama.update.jam-kerja');
    Route::post('/hari-kerja', [ApiController::class, 'updateHariKerja'])->name('api.admin-utama.update.hari-kerja');
    Route::post('/reset-password', [ApiController::class, 'resetPassword'])->name('api.admin-utama.update.reset-password');
    Route::post('/role', [ApiController::class, 'updateRole'])->name('api.admin-utama.update.role');
    // API UPDATE End

    // API DELETE Start (Admin Utama Only)
    Route::post('/delete/admin', [ApiController::class, 'deleteAdmin'])->name('api.admin-utama.delete.admin');
    // API DELETE End
});

// API Dashboard Start
Route::get('/get/dashboard', [ApiController::class, 'getDashboard'])->name('api.get.dashboard');
// API Dashboard End

// API Server-Sent Events (SSE) Start
Route::get('/get/sse', [ApiController::class, 'getSSE'])->name('api.get.sse');
// API Server-Sent Events (SSE) End
