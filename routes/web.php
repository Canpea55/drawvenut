<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', [BlogController::class, 'index']);
Route::get('/detail/{id}', [BlogController::class, 'detail']);

Route::prefix('author')->group(function () {
    Route::get('/blog', [AdminController::class, 'index'])->name('blog');
    Route::get('/form', [AdminController::class, 'createBlog'])->name('form');
    Route::post('/insert', [AdminController::class, 'insert']);
    Route::get('/delete/{id}', [AdminController::class, 'delete'])->name('delete');
    Route::get('/change/{id}', [AdminController::class, 'change'])->name('change');
    Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [AdminController::class, 'update'])->name('update');
});

Route::get('/about', [AdminController::class, 'about'])->name('about');

Route::get('blog/{name}', function ($name) {
    return "<h1>Blogs ${name}</h1>";
});

Route::get('admin', function () {
    return "<a href='admin/user/kong'>1.Kong</a>";
});
Route::get('admin/user/kong', function () {
    return "<h1>Hello Admin kong</h1>";
});

Route::get('admin2', function () {
    return "<a href='" . route('login') . "'>1.Kong</a>";
});
Route::get('admin2/user/kong', function () {
    return "<h1>Hello Admin kong</h1>
    you have been use a recommanded path";
})->name('login');

Route::fallback(function () {
    return "404 not found"; });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
