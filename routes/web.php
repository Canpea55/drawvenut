<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/home', [ShopController::class, 'index'])->name('home');
Route::get('/about', [ShopController::class, 'about'])->name('about');
Route::fallback(function () {return "404 not found"; });

Route::get('/search', [SearchController::class, 'search'])->name('search');

Route::get('/product/{id}', [ShopController::class, 'product'])->name('product');
// Route::get('blog/{name}', function ($name) {
//     return "<h1>Blogs ${name}</h1>";
// });

Route::prefix('administration')->group(function () {
    Route::get('/blog', [AdminController::class, 'index'])->name('blog');
    Route::get('/form', [AdminController::class, 'createBlog'])->name('form');
    Route::post('/insert', [AdminController::class, 'insert']);
    Route::get('/delete/{id}', [AdminController::class, 'delete'])->name('delete');
    Route::get('/change/{id}', [AdminController::class, 'change'])->name('change');
    Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [AdminController::class, 'update'])->name('update');
});

Auth::routes();
