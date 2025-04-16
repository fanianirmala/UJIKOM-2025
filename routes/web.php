<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;



Route::middleware('IsGuest')->group(function(){
    Route::get('/', [LoginController::class, 'loginForm'])->name('login');
    Route::post('/loginAuth', [LoginController::class, 'loginAuth'])->name('loginAuth');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/error/permission', function () {
    return view('error.permission');
})->name('error.permission');

Route::middleware(['IsLogin', 'IsAdmin'])->group(function(){
    Route::prefix('/admin')->name('admin.')->group(function(){
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        //USER
        Route::get('/user', [AdminController::class, 'userIndex'])->name('user');
        Route::get('/user/create', [AdminController::class, 'userCreate'])->name('user.create');
        Route::post('/user/store', [AdminController::class, 'userStore'])->name('user.store');
        Route::get('/user/edit/{id}', [AdminController::class, 'userEdit'])->name('user.edit');
        Route::put('/user/update/{id}', [AdminController::class, 'userUpdate'])->name('user.update');
        Route::delete('/user/delete/{id}', [AdminController::class, 'userDestroy'])->name('user.destroy');

        // PRODUK
        Route::get('/produk', [AdminController::class, 'produkIndex'])->name('produk');
        Route::get('/produk/create', [AdminController::class, 'produkCreate'])->name('produk.create');
        Route::post('/produk/store', [AdminController::class, 'produkStore'])->name('produk.store');
        Route::get('/produk/edit/{id}', [AdminController::class, 'produkEdit'])->name('produk.edit');
        Route::put('/produk/update/{id}', [AdminController::class, 'produkUpdate'])->name('produk.update');
        Route::put('/produk/stok/edit/{id}', [AdminController::class, 'produkStok'])->name('produk.stok');
        Route::delete('/produk/delete/{id}', [AdminController::class, 'produkDestroy'])->name('produk.destroy');
    });
});

Route::middleware(['IsLogin', 'IsPetugas'])->group(function(){
    Route::prefix('/petugas')->name('petugas.')->group(function(){
        Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');

        //PRODUK
        Route::get('/produk', [PetugasController::class, 'produkIndex'])->name('produk');

        //PEMBELIAN
        Route::get('/pembelian', [PetugasController::class, 'pembelianIndex'])->name('pembelian');
        Route::get('/create/pembelian', [PetugasController::class, 'createPenjualan'])->name('create.penjualan');
        Route::post('/sale/create', [PetugasController::class, 'saleCreate'])->name('sale.create');
        Route::post('/checkout', [PetugasController::class, 'nonMemberCheckout'])->name('checkout');
        Route::get('/non-member/struk/{id}', [PetugasController::class, 'nonMemberStruk'])->name('non-member.struk');


    });
});