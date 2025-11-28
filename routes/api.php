<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductVariantController;
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
        Route::get('/', [ProductController::class, 'index']); // List Products
        Route::get('{product}', [ProductController::class, 'show']); // Single Product

        Route::post('/', [ProductController::class, 'store']); // Create Product

        // Dynamic last
        Route::put('{product}', [ProductController::class, 'update']); // Update Product
        Route::delete('{product}', [ProductController::class, 'destroy']); // Delete Product

        //Product Images
        Route::get('{product}/images', [ProductImageController::class, 'index']); // List Product Image

        Route::post('images', [ProductImageController::class, 'store']); // Create Product Image

        Route::post('{product}/images/{image}/primary', [ProductImageController::class, 'setPrimary']); // Set Primary Product Image
        Route::post('{product}/images/reorder', [ProductImageController::class, 'reorder']); // Reorder Product Image

        Route::put('images/{id}', [ProductImageController::class, 'update']); // Update Product Image
        Route::delete('images/{id}', [ProductImageController::class, 'destroy']); // Delete Product Image

        //Product Variant
        Route::get('/{product}/variants', [ProductVariantController::class, 'index']);      // List variants
        Route::get('/{product}/variants/{variantId}', [ProductVariantController::class, 'show']); // Single variant

        Route::post('/{product}/variants', [ProductVariantController::class, 'store']);     // Create variant
        
        Route::put('/{product}/variants/{variant}', [ProductVariantController::class, 'update']); // Update variant
        Route::delete('/{product}/variants/{variantId}', [ProductVariantController::class, 'destroy']); // Delete
    });
});
