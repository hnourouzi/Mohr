<?php

use App\Http\Controllers\Api\V1\Admin\BlacklistController as AdminBlacklistController;
use App\Http\Controllers\Api\V1\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::post('auth/register', [AuthController::class, 'register'])
        ->middleware('throttle:register')
        ->name('auth.register');

    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:api-login')
        ->name('auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::get('wallet', [WalletController::class, 'show'])->name('wallet.show');
        Route::get('wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
        Route::post('wallet/top-up', [WalletController::class, 'topUp'])->name('wallet.top-up');

        Route::get('contents', [ContentController::class, 'index'])->name('contents.index');
        Route::post('contents', [ContentController::class, 'store'])
            ->middleware('throttle:api-content-submit')
            ->name('contents.store');
        Route::get('contents/{content}', [ContentController::class, 'show'])->name('contents.show');
        Route::get('contents/{content}/logs', [ContentController::class, 'logs'])->name('contents.logs');
        Route::post('contents/{content}/retry', [ContentController::class, 'retry'])->name('contents.retry');

        Route::prefix('admin')->middleware('admin')->name('admin.')->group(function (): void {
            Route::get('contents', [AdminContentController::class, 'index'])->name('contents.index');
            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
            Route::post('users/{user}/top-up', [AdminUserController::class, 'topUp'])->name('users.top-up');
            Route::get('blacklist', [AdminBlacklistController::class, 'index'])->name('blacklist.index');
            Route::post('blacklist', [AdminBlacklistController::class, 'store'])->name('blacklist.store');
            Route::delete('blacklist/{blacklistWord}', [AdminBlacklistController::class, 'destroy'])->name('blacklist.destroy');
        });
    });
});
