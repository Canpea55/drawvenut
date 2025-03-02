<?php

use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;

use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('index');
Route::get('/about', [ShopController::class, 'about'])->name('about');
Route::fallback(function () {return "404 not found"; });

Route::get('/product/{id}', [ShopController::class, 'product'])->name('product');

Route::middleware(['auth'])->group(function () {
    Route::get('/inventory', [ShopController::class, 'inventory'])->name("inventory");

    // cart system
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'cart'])->name('cart');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // order system
    Route::get('/orders', [OrderController::class, 'orders'])->name('orders');
    Route::get('/order/{id}', [OrderController::class, 'order'])->name('order');
    Route::post('/order/add', [OrderController::class, 'add'])->name('order.add');
    Route::post('/order/remove', [OrderController::class, 'remove'])->name('order.remove');
    Route::post('/order/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
});

Route::middleware(['auth', 'isAdmin'])->prefix('admin')->group(function () {
    // product management
    Route::get('/product/create', [AdminController::class, 'productCreate'])->name('productCreate');
    Route::get('/product/edit/{id}', [AdminController::class, 'productEdit'])->name('productEdit');
    Route::get('/product/list', [AdminController::class, 'productList'])->name('productList');
    Route::post('/product/insert', [AdminController::class, 'productInsert'])->name('productInsert');
    Route::get('/product/delete/{id}', [AdminController::class, 'productDelete'])->name('productDelete');
    Route::get('/product/change/{id}', [AdminController::class, 'productToggle'])->name('productToggle');
    Route::post('/product/update/{id}', [AdminController::class, 'productUpdate'])->name('productUpdate');
    
    // user management
    Route::get('/users/list', [AdminController::class, 'userList'])->name('userList');
    Route::get('/users/edit/{id}', [AdminController::class, 'userEdit'])->name('userEdit');
    Route::post('/users/update/{id}', [AdminController::class, 'userUpdate'])->name('userUpdate');
    Route::get('/users/delete/{id}', [AdminController::class, 'userDelete'])->name('userDelete');
});

Auth::routes();
