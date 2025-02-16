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

Route::middleware(['auth', 'isAdmin'])->prefix('administration')->group(function () {
    Route::get('/form', [AdminController::class, 'productForm'])->name('form');
    Route::post('/insert', [AdminController::class, 'productInsert']);

    Route::get('/blog', [AdminController::class, 'index'])->name('blog');
    Route::get('/delete/{id}', [AdminController::class, 'delete'])->name('delete');
    Route::get('/change/{id}', [AdminController::class, 'change'])->name('change');
    Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [AdminController::class, 'update'])->name('update');
});

Auth::routes();
