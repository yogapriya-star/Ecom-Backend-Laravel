<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Auth\Events\Verified;

// Public routes (Register, Login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Forgot/Reset password
Route::post('/password/forgot', [PasswordResetController::class, 'forgotPassword']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

// Email verification link
Route::get('/email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verify.email');

// Protected routes (Logout, Email resend verification link, Address )
Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['jwt.auth', 'role:customer'])->group(function () {
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    Route::post('/addresses/{id}/set-active', [AddressController::class, 'setActiveAddress']);
});

Route::middleware(['jwt.auth', 'role:super_admin|manager|employee'])->group(function () {
    Route::prefix('categories')->group(function () {

        // Static first
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/tree', [CategoryController::class, 'categoryTree']);
        Route::get('/parents', [CategoryController::class, 'parentCategories']);
        Route::get('/second-level', [CategoryController::class, 'secondLevelCategories']);

        Route::post('/', [CategoryController::class, 'store']);

        // Dynamic last
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });
});

Route::middleware(['jwt.auth', 'role:super_admin|manager|employee'])->group(function () {
    Route::prefix('products')->group(function () {

        // Static first
        Route::get('/', [ProductController::class, 'index']);
        Route::get('{product}', [ProductController::class, 'show']);

        Route::post('/', [ProductController::class, 'store']);

        // Dynamic last
        Route::put('{product}', [ProductController::class, 'update']);
        Route::delete('{product}', [ProductController::class, 'destroy']);
    });
});
