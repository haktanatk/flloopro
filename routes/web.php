<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ThankYouController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MyOrdersController;
use App\Models\Basket;

/* ========================== Public ========================== */

// Ana sayfa ve /shop aynı controller (view'a $products gider)
Route::get('/',     [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'index'])->name('shop');

// Kategori sayfan (isteğe bağlı statik)
Route::view('/kategori', 'pages.category')->name('kategori');

// Kategoriler – HomeController (senin randomProductsByGender akışı)
Route::get('/kadin', [HomeController::class, 'kadin'])->name('kadin');
Route::get('/erkek', [HomeController::class, 'erkek'])->name('erkek');
Route::get('/cocuk', [HomeController::class, 'cocuk'])->name('cocuk');

// Eski controller tabanlı rotaların için alias'lar (kodlarında geçiyorsa bozmasın)
Route::get('/kadin-urunleri', [ProductController::class, 'kadinUrunleri'])->name('kadin.urunleri');
Route::get('/erkek-urunleri', [ProductController::class, 'erkekUrunleri'])->name('erkek.urunleri');
Route::get('/cocuk-urunleri', [ProductController::class, 'cocukUrunleri'])->name('cocuk.urunleri');

// Ürün detay (kanonik)
Route::get('/product/{product}/{slug?}', [ProductController::class, 'show'])
    ->whereNumber('product')
    ->name('product.show');
// Eski /urun/... linkleri için kalıcı yönlendirme
Route::get('/urun/{product}/{slug?}', function ($product, $slug = null) {
    return redirect()->route('product.show', ['product' => $product, 'slug' => $slug], 301);
});

/* ========================== Cart API ========================== */
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');      // mini-cart getir
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');     // sepete ekle
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy'); // sepetten sil (id)
Route::get('/sepetim/json', [CartController::class, 'index']);
Route::post('/cart/update',    [CartController::class, 'update'])->name('cart.update'); // <-- EKLENDİ
// eski alias

/* ========================== Checkout ========================== */
// Boş sepetle checkout'a girilmesin
Route::get('/checkout', function () {
    $basket = Basket::currentFor(request());
    if (!$basket || $basket->items()->count() === 0) {
        return redirect()->route('shop')->with('warn', 'Sepetiniz boş.');
    }
    return view('pages.checkout');
})->name('checkout');

Route::post('/checkout/review',  [CheckoutController::class, 'review'])->name('checkout.review');
Route::post('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');

/* ========================== Thank You ========================== */
Route::get('/tesekkur/{orderNo?}', [ThankYouController::class, 'show'])->name('thankyou');
Route::get('/thankyou', fn () => redirect()->route('thankyou')); // İngilizce alias

/* ========================== Breeze / Auth ========================== */
// Dashboard isteyen paketler için alias → shop
Route::get('/dashboard', fn () => redirect()->route('shop'))->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/hesabim/siparisler', [MyOrdersController::class, 'index'])->name('my.orders');
});



// Breeze auth rotaları (/login, /register, /logout vs.)
require __DIR__ . '/auth.php';
