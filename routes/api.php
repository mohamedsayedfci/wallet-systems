<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\WalletController;
use Illuminate\Support\Facades\Route;

/**
 * Define API routes for wallet operations.
 *
 * Routes are protected by Passport authentication to ensure only authorized users can access wallet endpoints.
 */

/**
 * Define API routes for authentication and user management.
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::get('/transactions', [TransactionController::class, 'index']);

});
