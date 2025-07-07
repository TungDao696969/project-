<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\User\HomeController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => 'checkAdmin'
], function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::group([
        'prefix' => 'categories',
        'as' => 'categories.'
    ], function () {
        Route::get('/', [CategoryController::class, 'listCategory'])->name('listCategory');
        Route::get('add-category', [CategoryController::class, 'addCategory'])->name('addCategory');
        Route::post('add-category', [CategoryController::class, 'addPostCategory'])->name('addPostCategory');
        Route::delete('delete-category/{id}', [CategoryController::class, 'deleteCategory'])->name('deleteCategory');
        Route::get('detail-category/{id}', [CategoryController::class, 'detailCategory'])->name('detailCategory');
        Route::get('update-category/{id}', [CategoryController::class, 'updateCategory'])->name('updateCategory');
        Route::patch('update-category/{id}', [CategoryController::class, 'updatePatchCategory'])->name('updatePatchCategory');
    });
    Route::resource('products', ProductController::class);


});

Route::get('/', function () {
    return redirect()->route('client.home');
});

Route::group([
    'prefix' => 'client',
    'as' => 'client.',
], function () {
    Route::get('home', [HomeController::class, 'home'])->name('home');
    Route::get('/product/{id}', [ProductController::class, 'showProduct'])->name('showProduct');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');



Route::group(['prefix' => 'cart', 'as' => 'cart.', 'middleware' => 'auth'], function () {
    Route::get('list-cart', [CartController::class, 'listCart'])->name('listCart');
    Route::post('add-cart', [CartController::class, 'addToCart'])->name('addToCart');
});

Route::group(['prefix' => 'checkout', 'as' => 'checkout.', 'middleware' => 'auth'], function () {
    Route::get('/', [CheckoutController::class, 'showCheckoutForm'])->name('showCheckoutForm');
    Route::post('/', [CheckoutController::class, 'processCheckout'])->name('process');
    Route::get('/trackOrder', [CheckoutController::class, 'trackOrder'])->name('trackOrder');
    Route::post('/orders/{order}/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
    Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('coupon');
});