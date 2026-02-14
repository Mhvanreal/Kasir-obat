<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ObatController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route khusus Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::resource('obat', ObatController::class);

});

// Route khusus Kasir
Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->group(function () {
    Route::get('/transaksi', function () {
        return view('kasir.transaksi');
    })->name('kasir.transaksi');
});

// Route khusus Apoteker
Route::middleware(['auth', 'role:apoteker,admin'])->prefix('apoteker')->name('apoteker.')->group(function () {
    Route::resource('obat', ObatController::class);
});

// Route khusus Owner
Route::middleware(['auth', 'role:owner,admin'])->prefix('owner')->group(function () {
    Route::get('/laporan', function () {
        return view('owner.laporan');
    })->name('owner.laporan');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
