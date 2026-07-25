<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::post('/checkout/decrement-stock', [CheckoutController::class, 'decrementStock']);
