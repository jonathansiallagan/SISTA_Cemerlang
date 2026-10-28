<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CsDashboardController;
use App\Http\Controllers\LaporanLabaRugiController;

// Halaman awal: arahkan sesuai status login & role
Route::get('/', function () {
    if (Auth::user()->role === 'Customer Service') {
        return redirect()->route('cs.dashboard');
    }
    
    return redirect()->route('admin.dashboard');
})->middleware('auth');

// AREA PUBLIK //
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
});

//====================================================================================================//
// AREA INTERNAL //
Route::middleware(['auth'])->group(function () {

    // Rute Dashboard Admin (Untuk Pusat & Cabang)
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Rute Dashboard CS
    Route::get('/cs', [CsDashboardController::class, 'index'])->name('cs.dashboard');

    // Halaman Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route untuk Profil
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profil/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profil/preferensi', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    // Route khusus untuk upload avatar via AJAX
    Route::post('/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

//====================================================================================================//

    // === ROUTE TRANSAKSI (Admin Pusat, Admin Cabang, Customer Service) ===
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/tambah', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/export/pdf', [TransaksiController::class, 'exportPdf',])->name('transaksi.export.pdf');
    Route::get('/transaksi/export/excel', [TransaksiController::class,'exportExcel',])->name('transaksi.export.excel');

//====================================================================================================//
    // === ROUTE yang bisa diakses ADMIN PUSAT & ADMIN CABANG ===
    Route::middleware(['role:Admin Pusat,Admin Cabang'])->group(function () {

        // === ROUTE PENGELUARAN ===
        Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
        Route::get('/pengeluaran/tambah', [PengeluaranController::class, 'create'])->name('pengeluaran.create');
        Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    
        // === ROUTE MASTER DATA PRODUK ===
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/tambah', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

        // === ROUTE JURNAL UMUM ===
        Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');

        // === ROUTE BUKU BESAR ===
        Route::get('/buku-besar', [BukuBesarController::class, 'index'])->name('buku_besar.index');
        Route::get('/buku-besar/export/pdf', [
            BukuBesarController::class,
            'cetakPdf',
        ])->name('buku_besar.pdf');

        Route::get('/buku-besar/export/excel', [
            BukuBesarController::class,
            'cetakExcel',
        ])->name('buku_besar.excel');

        // === ROUTE LAPORAN LABA RUGI ===
        Route::get('/laporan-laba-rugi', [
            LaporanLabaRugiController::class,
            'index'
        ])->name('laporan_laba_rugi.index');
            });

//====================================================================================================//
    // === ROUTE yang hanya bisa diakses ADMIN PUSAT ===
    Route::middleware(['role:Admin Pusat'])->group(function () {
        // === ROUTE MASTER DATA CABANG ===
        
        // === ROUTE MASTER DATA CABANG ===
        Route::get('/cabang', [CabangController::class, 'index'])
            ->name('cabang.index');

        Route::get('/cabang/tambah', [CabangController::class, 'create'])
            ->name('cabang.create');

        Route::post('/cabang', [CabangController::class, 'store'])
            ->name('cabang.store');

        Route::get('/cabang/{id}/edit', [CabangController::class, 'edit'])
            ->name('cabang.edit');

        Route::put('/cabang/{id}', [CabangController::class, 'update'])
            ->name('cabang.update');

        Route::patch('/cabang/{id}/status', [CabangController::class, 'toggleStatus'])
            ->name('cabang.toggle-status');

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
        Route::get('/akun/preview-kode', [AkunController::class, 'previewKode'])->name('akun.preview-kode');
    });
});