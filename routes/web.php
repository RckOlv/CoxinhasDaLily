<?php

use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/productos', [CatalogController::class, 'index'])->name('catalog.index');

Route::prefix('admin')->group(function () {
    Route::get('/productos', [AdminProductController::class, 'index'])->name('admin.products');
    Route::put('/productos/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/productos/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
});
