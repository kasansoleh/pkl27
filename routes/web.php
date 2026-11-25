<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BengkelController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Bengkel PKL (All roles can view list)
    Route::get('/bengkel', [BengkelController::class, 'index'])->name('bengkel.index');

    // Siswa Action: Pilih Bengkel
    Route::post('/bengkel/pilih', [PendaftaranController::class, 'pilihBengkel'])->name('bengkel.pilih');

    // Guru & Admin Actions (CRUD Bengkel, Users, Confirmations, Exports)
    Route::middleware(\App\Http\Middleware\EnsureAdminOrGuru::class)->group(function () {
        // Kelola Bengkel
        Route::post('/bengkel', [BengkelController::class, 'store'])->name('bengkel.store');
        Route::put('/bengkel/{bengkel}', [BengkelController::class, 'update'])->name('bengkel.update');
        Route::delete('/bengkel/{bengkel}', [BengkelController::class, 'destroy'])->name('bengkel.destroy');
        Route::get('/bengkel-export', [BengkelController::class, 'exportExcel'])->name('bengkel.export');
        Route::post('/bengkel-import', [BengkelController::class, 'importExcel'])->name('bengkel.import');
        Route::get('/bengkel-template', [BengkelController::class, 'downloadTemplate'])->name('bengkel.template');

        // Pengajuan PKL & Konfirmasi
        Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::post('/pendaftaran/{pendaftaran}/konfirmasi', [PendaftaranController::class, 'konfirmasi'])->name('pendaftaran.konfirmasi');
        Route::delete('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

        // Siswa Belum Memilih & Export Laporan
        Route::get('/siswa-belum-memilih', [PendaftaranController::class, 'belumMemilih'])->name('pendaftaran.belum-memilih');
        Route::get('/export-siswa-diterima', [PendaftaranController::class, 'exportDiterima'])->name('pendaftaran.export-diterima');
        Route::get('/export-siswa-belum-memilih', [PendaftaranController::class, 'exportBelumMemilih'])->name('pendaftaran.export-belum-memilih');

        // Kelola Akun Siswa & Guru
        Route::get('/users', [UserController::class, 'index'])->name('user.index');
        Route::post('/users', [UserController::class, 'store'])->name('user.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('user.destroy');
        Route::get('/users-export', [UserController::class, 'exportExcel'])->name('user.export');
        Route::post('/users-import', [UserController::class, 'importExcel'])->name('user.import');
        Route::get('/users-template', [UserController::class, 'downloadTemplate'])->name('user.template');
    });
});
