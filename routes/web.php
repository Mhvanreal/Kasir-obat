<?php

use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatTransaksiController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Root URL: kalau user sudah login, arahkan ke dashboard. Kalau tidak, ke login.
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route khusus Admin
Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('dashboard');
    Route::resource('obat', ObatController::class);
    Route::resource('supplier', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('pelanggan', PelangganController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian');
    Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/export', [LaporanController::class, 'exportCsv'])->name('laporan.export');
    Route::get('/laporan/cetak', [LaporanController::class, 'printReport'])->name('laporan.cetak');
    Route::resource('users', UserController::class);
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/qris', [PengaturanController::class, 'updateQris'])->name('pengaturan.qris');
    Route::post('/pengaturan/general', [PengaturanController::class, 'updateGeneral'])->name('pengaturan.general');
});

// Route khusus Karyawan (menggabungkan Kasir + Apoteker)
Route::middleware(['auth', 'role:karyawan,admin,owner'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/transaksi', [PenjualanController::class, 'index'])->name('transaksi');
    Route::post('/transaksi', [PenjualanController::class, 'store'])->name('transaksi.store');
    Route::post('/pelanggan', [PelangganController::class, 'quickStore'])->name('pelanggan.quick');
    Route::resource('obat', ObatController::class);
    Route::get('/riwayat-transaksi', [RiwayatTransaksiController::class, 'index'])->name('riwayat-transaksi.index');
    Route::get('/riwayat-transaksi/{nota}', [RiwayatTransaksiController::class, 'show'])->name('riwayat-transaksi.show');
    Route::get('/riwayat-transaksi/{nota}/cetak-pdf', [RiwayatTransaksiController::class, 'cetakPdf'])->name('riwayat-transaksi.cetak-pdf');
    Route::get('/riwayat-transaksi/{nota}/cetak-thermal', [RiwayatTransaksiController::class, 'cetakThermal'])->name('riwayat-transaksi.cetak-thermal');
});

// Route khusus Owner (share controller yang sama dengan admin.laporan)
Route::middleware(['auth', 'role:owner,admin'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
});

// Route khusus Owner-only (admin & karyawan tidak boleh)
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::resource('pengeluaran', PengeluaranController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
