<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Kategori;
use App\Http\Livewire\Produk;
use App\Http\Livewire\Daftar;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('produk', 'produk')
    ->middleware(['auth', 'verified'])
    ->name('produk');
    
Route::view('kategori', 'kategori')
    ->middleware(['auth', 'verified'])
    ->name('kategori');

Route::view('daftar', 'daftar')
    ->middleware(['auth', 'verified'])
    ->name('daftar'); 

Route::view('dashboard_user', 'dashboard_user')
    ->middleware(['auth', 'verified'])
    ->name('dashboard_user');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
