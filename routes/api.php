<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Публичные маршруты (доступны без токена)
    Route::post('register', [AuthController::class, 'register']);

    // Логин тоже публичный, но защищен от подбора паролей (5 попыток в минуту)
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');

    // Витрина товаров доступна всем
    Route::get('products', [ProductController::class, 'index']);

    // ЗАЩИЩЕННЫЕ маршруты (только с токеном)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::post('orders', [OrderController::class, 'store']); // <-- ДОБАВИТЬ
        Route::get('orders', [OrderController::class, 'index']); // <-- НОВЫЙ МАРШРУТ

    });
});
