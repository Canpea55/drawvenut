<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/about', [ShopController::class, 'about'])->name('about');
Route::fallback(function () {return "404 not found"; });

// Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/store', [ShopController::class, 'store'])->name('name');
Route::get('/product/{id}', [ShopController::class, 'product'])->name('product');

Route::prefix('customer')->group(function () {
    Route::get('/inventory', [ShopController::class, 'inventory'])->name("inventory");
});

Route::middleware(['auth', 'isAdmin'])->prefix('admin')->group(function () {
    //product
    Route::get('/product/create', [AdminController::class, 'productCreate'])->name('productCreate');
    Route::get('/product/edit/{id}', [AdminController::class, 'productEdit'])->name('productEdit');
    Route::get('/product/list', [AdminController::class, 'productList'])->name('productList');
    Route::post('/product/insert', [AdminController::class, 'productInsert'])->name('productInsert');
    Route::get('/product/delete/{id}', [AdminController::class, 'productDelete'])->name('productDelete');
    Route::get('/product/change/{id}', [AdminController::class, 'productToggle'])->name('productToggle');
    Route::post('/product/update/{id}', [AdminController::class, 'productUpdate'])->name('productUpdate');
    
    //user
    Route::get('/users/list', [AdminController::class, 'userList'])->name('userList');
    Route::get('/users/edit/{id}', [AdminController::class, 'userEdit'])->name('userEdit');
    Route::post('/users/update/{id}', [AdminController::class, 'userUpdate'])->name('userUpdate');
    Route::get('/users/delete/{id}', [AdminController::class, 'userDelete'])->name('userDelete');
});

Auth::routes();
