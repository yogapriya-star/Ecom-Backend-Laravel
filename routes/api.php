<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Auth\Events\Verified;

// Public routes (Register, Login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Forgot & Reset Password
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Email verification link
Route::get('/email/verify/{id}', [AuthController::class, 'verifyEmail'])
     ->name('verify.email');

// Protected routes (Logout, Email resend verification link )
Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
