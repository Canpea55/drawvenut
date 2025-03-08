<?php

use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('index');
Route::get('/about', [ShopController::class, 'about'])->name('about');
Route::fallback(function () {return "404 not found"; });

Route::get('/product/{id}', [ShopController::class, 'product'])->name('product');

Route::middleware(['auth'])->group(function () {
    Route::get('/library', [LibraryController::class, 'index'])->name("library");
    Route::get('/library/product/{id}', [LibraryController::class, 'info'])->name('library.product.info');

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
    Route::get('/product/add', [ProductController::class, 'add'])->name('product.add');
    Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::get('/product/manage', [ProductController::class, 'manage'])->name('product.manage');
    Route::post('/product/insert', [ProductController::class, 'insert'])->name('product.insert');
    Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');
    Route::get('/product/change/{id}', [ProductController::class, 'toggle'])->name('product.toggle');
    
    // user management
    Route::get('/users', [UserController::class, 'users'])->name('users');
    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('/users/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
});

Auth::routes();
