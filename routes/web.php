<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// home: index yok, senin yapına göre shop'u ana sayfa yapıyorum
Route::view('/', 'pages.shop')->name('home');

// statik sayfalar
Route::view('/shop', 'pages.shop')->name('shop');
Route::view('/checkout', 'pages.checkout')->name('checkout');
Route::view('/kadin', 'pages.kadin')->name('kadin');
Route::view('/cocuk', 'pages.cocuk')->name('cocuk');
Route::view('/kategori', 'pages.category')->name('kategori'); // istersen

// ERKEK ürünleri (dinamik: DB'den $products gönderir)


Route::get('/',      [HomeController::class, 'index'])->name('home');
Route::get('/kadin', [HomeController::class, 'kadin'])->name('kadin');
Route::get('/erkek', [HomeController::class, 'erkek'])->name('erkek');
Route::get('/cocuk', [HomeController::class, 'cocuk'])->name('cocuk');

Route::view('/checkout', 'pages.checkout')->name('checkout');

Route::post('/cart', [CartController::class, 'store']);
Route::delete('/cart/{id}', [CartController::class, 'destroy']);
Route::get('/cart', [CartController::class, 'index']);

Route::get('/urun/{product}/{slug?}', [ProductController::class, 'urunDetay'])
    ->name('product.show');

Route::get('/product/{product}/{slug?}', [ProductController::class, 'show'])
    ->whereNumber('product')
    ->name('product.show');

Route::get('/sepetim/json', [CartController::class, 'index']);


