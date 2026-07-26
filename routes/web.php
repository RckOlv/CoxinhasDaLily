<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/productos', [CatalogController::class, 'index'])->name('catalog.index');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/productos', [AdminProductController::class, 'index'])->name('admin.products');
    Route::post('/productos', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::put('/productos/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/productos/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');

    Route::get('/categorias', [AdminCategoryController::class, 'index'])->name('admin.categories');
    Route::post('/categorias', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categorias/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categorias/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
});

require __DIR__.'/auth.php';
