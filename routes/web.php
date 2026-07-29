<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminGalleryController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminVideoController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\EventFormController;
use App\Http\Controllers\PushController;
use App\Models\GalleryImage;
use App\Models\Video;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $gallery_images = GalleryImage::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    $videos = Video::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    return Inertia::render('Home', compact('gallery_images', 'videos'));
})->name('home');

Route::get('/productos', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/eventos', [EventFormController::class, 'show'])->name('events.form');
Route::post('/eventos', [EventFormController::class, 'store'])->name('events.store');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/productos', [AdminProductController::class, 'index'])->name('admin.products');
    Route::post('/productos', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::put('/productos/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/productos/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::put('/productos/{product}/stock', [AdminProductController::class, 'updateStock'])->name('admin.products.stock');

    Route::get('/categorias', [AdminCategoryController::class, 'index'])->name('admin.categories');
    Route::post('/categorias', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categorias/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categorias/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('/eventos', [AdminEventController::class, 'index'])->name('admin.events');
    Route::put('/eventos/{event}', [AdminEventController::class, 'update'])->name('admin.events.update');
    Route::put('/eventos/{event}/productos', [AdminEventController::class, 'updateProductQuantities'])->name('admin.events.products');

    Route::get('/pedidos', [AdminOrderController::class, 'index'])->name('admin.orders');
    Route::put('/pedidos/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');

    Route::get('/galeria', [AdminGalleryController::class, 'index'])->name('admin.gallery');
    Route::post('/galeria', [AdminGalleryController::class, 'store'])->name('admin.gallery.store');
    Route::put('/galeria/{image}', [AdminGalleryController::class, 'update'])->name('admin.gallery.update');
    Route::delete('/galeria/{image}', [AdminGalleryController::class, 'destroy'])->name('admin.gallery.destroy');

    Route::get('/videos', [AdminVideoController::class, 'index'])->name('admin.videos');
    Route::post('/videos', [AdminVideoController::class, 'store'])->name('admin.videos.store');
    Route::put('/videos/{video}', [AdminVideoController::class, 'update'])->name('admin.videos.update');
    Route::delete('/videos/{video}', [AdminVideoController::class, 'destroy'])->name('admin.videos.destroy');

    Route::get('/push/status', [PushController::class, 'status'])->name('admin.push.status');
    Route::post('/push/send', [PushController::class, 'send'])->name('admin.push.send');
});

Route::post('/api/push/subscribe', [PushController::class, 'subscribe'])->name('push.subscribe');
Route::post('/api/push/unsubscribe', [PushController::class, 'unsubscribe'])->name('push.unsubscribe');

require __DIR__.'/auth.php';
