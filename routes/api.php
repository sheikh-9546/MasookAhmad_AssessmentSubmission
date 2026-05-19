<?php

use App\Http\Controllers\V1\Auth\AuthenticateController;
use App\Http\Controllers\V1\Permission\PermissionController;
use App\Http\Controllers\V1\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('oauth')->group(function () {
    Route::post('login', [AuthenticateController::class, 'login'])->name('login');
    // Route::post('password', [PasswordController::class, 'password'])->name('password');
    // Route::patch('password', [PasswordController::class, 'resetPassword'])->name('reset.password');
    // Route::patch('set-password', [PasswordController::class, 'setPassword'])->name('set.password');
    // Route::post('verify-issued-token', [PasswordController::class, 'verifyIssuedToken'])->name('verify.issued.token');
    // Route::post('onboard-verify-issued-token', [PasswordController::class, 'verifyOnboardIssuedToken'])->name('verify.onboard.token');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('user', UserController::class)->except(['edit', 'create']);
    Route::resource('permission', PermissionController::class)->except(['edit', 'create']);
});
