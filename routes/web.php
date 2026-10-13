<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\ProfileController;

// Halaman awal: arahkan sesuai status login & role
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->homeRoute())
        : redirect()->route('login');
});

// AREA PUBLIK //
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
});

//====================================================================================================//
// AREA INTERNAL //
Route::middleware(['auth'])->group(function () {

    // === DASHBOARD (dipisah per role) ===
    Route::middleware(['role:Admin Pusat,Admin Cabang'])->group(function () {
        Route::get('/admin', [DashboardController::class, 'admin'])->name('admin');
    });
    Route::middleware(['role:Customer Service'])->group(function () {
        Route::get('/cs', [DashboardController::class, 'cs'])->name('cs');
    });

    // Halaman Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route untuk Register
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');

    // Route untuk Profil
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');

//====================================================================================================//
    // === ROUTE TRANSAKSI (Admin Pusat, Admin Cabang, Customer Service) ===
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/tambah', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');

//====================================================================================================//
    // === ROUTE yang bisa diakses ADMIN PUSAT & ADMIN CABANG ===
    Route::middleware(['role:Admin Pusat,Admin Cabang'])->group(function () {

        // === ROUTE MASTER DATA PRODUK ===
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/tambah', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

        // === ROUTE JURNAL UMUM ===
        Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
        Route::get('/jurnal/tambah', [JurnalController::class, 'create'])->name('jurnal.create');
        Route::post('/jurnal', [JurnalController::class, 'store'])->name('jurnal.store');

        // === ROUTE BUKU BESAR ===
        Route::get('/buku-besar', [BukuBesarController::class, 'index'])->name('buku_besar.index');
    });

//====================================================================================================//
    // === ROUTE yang hanya bisa diakses ADMIN PUSAT ===
    Route::middleware(['role:Admin Pusat'])->group(function () {
        // === ROUTE MASTER DATA CABANG ===
        Route::get('/cabang', [CabangController::class, 'index'])->name('cabang.index');
        Route::get('/cabang/tambah', [CabangController::class, 'create'])->name('cabang.create');
        Route::post('/cabang', [CabangController::class, 'store'])->name('cabang.store');

        // === ROUTE MASTER DATA USER ===
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::get('/user/tambah', [UserController::class, 'create'])->name('user.create');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

        // === ROUTE MASTER DATA AKUN ===
        Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
        Route::get('/akun/tambah', [AkunController::class, 'create'])->name('akun.create');
        Route::post('/akun', [AkunController::class, 'store'])->name('akun.store');
        Route::get('/akun/{id}/edit', [AkunController::class, 'edit'])->name('akun.edit');
        Route::put('/akun/{id}', [AkunController::class, 'update'])->name('akun.update');
        Route::delete('/akun/{id}', [AkunController::class, 'destroy'])->name('akun.destroy');
    });
});