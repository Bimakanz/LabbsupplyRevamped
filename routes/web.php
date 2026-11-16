<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home-{variant}', [HomeController::class, 'variant'])->name('home.variant')->where('variant', '0[1-8]');

Route::get('/semua-produk', [ShopController::class, 'index'])->name('shop');
Route::get('/product-category/{category}', [ShopController::class, 'category'])->name('category.show');
Route::get('/product-tag/{tag}', [ShopController::class, 'tag'])->name('tag.show');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/search', [ShopController::class, 'search'])->name('search');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/cart/mini', [CartController::class, 'mini'])->name('cart.mini');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/{order}/confirm', [PaymentController::class, 'confirm'])->name('payment.confirm');
Route::get('/payment/{order}/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/payment/mayar/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
Route::post('/compare/toggle', [CompareController::class, 'toggle'])->name('compare.toggle');
Route::post('/compare/remove', [CompareController::class, 'remove'])->name('compare.remove');
Route::post('/compare/clear', [CompareController::class, 'clear'])->name('compare.clear');
Route::get('/compare/bar', [CompareController::class, 'bar'])->name('compare.bar');

Route::get('/request-quote', [QuoteController::class, 'index'])->name('quote.index');
Route::post('/request-quote', [QuoteController::class, 'store'])->name('quote.store');
Route::post('/request-quote/add', [QuoteController::class, 'add'])->name('quote.add');
Route::post('/request-quote/remove', [QuoteController::class, 'remove'])->name('quote.remove');

Route::get('/download', [DownloadController::class, 'index'])->name('download.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/hubungi-kami', [ContactController::class, 'index'])->name('contact.index');
Route::post('/hubungi-kami', [ContactController::class, 'store'])->name('contact.store');
Route::get('/order-tracking', [OrderTrackingController::class, 'index'])->name('order.track');
Route::post('/order-tracking', [OrderTrackingController::class, 'track'])->name('order.track.post');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AccountController::class, 'loginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login'])->name('login.store');
    Route::get('/register', [AccountController::class, 'registerForm'])->name('register');
    Route::post('/register', [AccountController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
    Route::get('/my-account', [AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/my-account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/my-account/orders/{order}', [AccountController::class, 'order'])->name('account.order');
    Route::get('/my-account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/my-account/profile', [AccountController::class, 'updateProfile'])->name('account.profile');
});

Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/quick-view/{product}', [ProductController::class, 'quickView'])->name('product.quick-view');
Route::get('/search/suggest', [ShopController::class, 'suggest'])->name('search.suggest');

Route::get('/storage/{path}', function (string $path) {
    $root = realpath(storage_path('app/public'));
    $file = realpath(storage_path('app/public/'.$path));

    abort_unless($root && $file && str_starts_with($file, $root) && is_file($file), 404);

    return response()->file($file);
})->where('path', '.*')->name('storage.fallback');

Route::get('/{page}', [PageController::class, 'show'])->name('page.show');
