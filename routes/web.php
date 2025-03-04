<?php

use App\Http\Controllers\LibraryController;
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
    Route::get('/library', [LibraryController::class, 'index'])->name("library");

    // cart system
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'cart'])->name('cart');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // order system
    Route::get('/orders', [OrderController::class, 'orders'])->name('orders');
    Route::get('/order/{id}', [OrderController::class, 'order'])->name('order');
    Route::post('/order/add', [OrderController::class, 'add'])->name('order.add');
    Route::post('/order/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::post('/order/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
});

Route::middleware(['auth', 'isAdmin'])->prefix('admin')->group(function () {
    // order management
    Route::get('/order/manage', [OrderController::class, 'manage'])->name('order.manage');
    Route::post('/order/approve', [OrderController::class, 'approve'])->name('order.approve');
    Route::post('/order/remove', [OrderController::class, 'remove'])->name('order.remove');
    
    // library management
    Route::get('/library/manage', [LibraryController::class, 'manage'])->name('library.manage');

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
