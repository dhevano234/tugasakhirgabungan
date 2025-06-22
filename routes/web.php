<?php

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

// Routes Protected by Auth
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Profile Routes - DIPERBAIKI
    Route::get('/editprofile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Riwayat Pasien
    Route::get('/riwayatkunjungan', [RiwayatController::class, 'index'])->name('riwayat.index');

    // Antrian Routes
    Route::prefix('antrian')->name('antrian.')->group(function () {
        Route::get('/', [AntrianController::class, 'index'])->name('index');
        
        // Route untuk membuat antrian baru
        Route::get('/ambil', [AntrianController::class, 'create'])->name('create');
        Route::post('/', [AntrianController::class, 'store'])->name('store');
        
        // Route untuk detail antrian
        Route::get('/{id}', [AntrianController::class, 'show'])->name('show');
        
        // Route untuk edit antrian
        Route::get('/{id}/edit', [AntrianController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AntrianController::class, 'update'])->name('update');
        
        // Route untuk batalkan antrian
        Route::delete('/{id}', [AntrianController::class, 'destroy'])->name('destroy');
        
        // Route untuk print tiket (HTML view)
        Route::get('/{id}/print', [AntrianController::class, 'print'])->name('print');
        
        // Route untuk download PDF tiket
        Route::get('/{id}/download-pdf', [AntrianController::class, 'downloadPdf'])->name('downloadPdf');
    });

    // Jadwal Dokter
    Route::get('/jadwaldokter', [DoctorController::class, 'jadwaldokter'])->name('jadwaldokter');
});