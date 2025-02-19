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
    Route::get('/product/form', [AdminController::class, 'productForm'])->name('productForm');
    Route::post('/product/insert', [AdminController::class, 'productInsert'])->name('productInsert');
    Route::get('/product/list', [AdminController::class, 'productList'])->name('productList');
    Route::get('/product/delete/{id}', [AdminController::class, 'productDelete'])->name('productDelete');
    Route::get('/product/change/{id}', [AdminController::class, 'productToggle'])->name('productToggle');
    Route::get('/product/edit/{id}', [AdminController::class, 'productEdit'])->name('productEdit');
    Route::post('/product/update/{id}', [AdminController::class, 'productUpdate'])->name('productUpdate');
});

Auth::routes();
