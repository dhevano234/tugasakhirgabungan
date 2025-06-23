<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;

// Halaman Utama
Route::get('/', fn () => view('welcome'))->name('welcome');

// Auth: Login & Register
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register');
});

// Password Reset Routes
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Routes untuk USER/PASIEN SAJA
Route::middleware(['auth', 'role.user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Profile Routes
    Route::get('/editprofile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Riwayat Pasien
    Route::get('/riwayatkunjungan', [RiwayatController::class, 'index'])->name('riwayat.index');

    // Antrian Routes - HANYA UNTUK USER/PASIEN
    Route::prefix('antrian')->name('antrian.')->group(function () {
        Route::get('/', [AntrianController::class, 'index'])->name('index');
        Route::get('/create', [AntrianController::class, 'create'])->name('create');
        Route::post('/store', [AntrianController::class, 'store'])->name('store');
        Route::get('/status/{queue}', [AntrianController::class, 'show'])->name('show');
        Route::delete('/cancel/{queue}', [AntrianController::class, 'cancel'])->name('cancel');
        Route::get('/ticket/{queue}', [AntrianController::class, 'ticket'])->name('ticket');
    });

    // Doctor Info - Untuk pasien lihat info dokter
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/{schedule}', [DoctorController::class, 'show'])->name('doctors.show');
    Route::get('/jadwaldokter', [DoctorController::class, 'jadwaldokter'])->name('jadwaldokter');
});

// PENTING: Panel Admin dan Dokter menggunakan middleware di PanelProvider masing-masing
// Tidak perlu ditambah middleware di web.php karena Filament menghandle sendiri