<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AddressController;
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

    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    Route::post('/addresses/{id}/set-active', [AddressController::class, 'setActiveAddress']);


    Route::post('/logout', [AuthController::class, 'logout']);
});


