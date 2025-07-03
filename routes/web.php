<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\AuthController;
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

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');



Route::group(['prefix' => 'cart', 'as' => 'cart.', 'middleware' => 'auth'], function () {
    Route::get('list-cart', [CartController::class, 'listCart'])->name('listCart');
    Route::post('add-cart', [CartController::class, 'addToCart'])->name('addToCart');
});