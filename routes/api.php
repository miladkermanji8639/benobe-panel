<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;




Route::prefix('/auth')->group(function () {
    Route::post('/login-register', [AuthController::class, 'loginRegister'])->name('api.auth.login-register');
    Route::post('/login-confirm/{token}', [AuthController::class, 'loginConfirm'])->name('api.auth.login-confirm');
    Route::post('/resend-otp/{token}', [AuthController::class, 'resendOtp'])->name('api.auth.resend-otp');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/profile', [AuthController::class, 'me'])->middleware('jwt.auth');
    Route::get('/verify-token', [AuthController::class, 'verifyToken'])->middleware('jwt.auth');
});



