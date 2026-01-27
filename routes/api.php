<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

    // Protected routes - require authentication
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

        // Product routes
        Route::apiResource('products', ProductController::class);

        // Thumbnail upload
        Route::post('/products/{product}/thumbnail', [ProductController::class, 'uploadThumbnail'])
            ->name('products.uploadThumbnail');
    });
});
