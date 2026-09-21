<?php

use App\Http\Controllers\Admin\BlacklistController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('users/{user}/top-up', [UserController::class, 'topUp'])->name('users.topup');

    Route::get('contents', [ContentController::class, 'index'])->name('contents.index');
    Route::get('contents/{content}', [ContentController::class, 'show'])->name('contents.show');

    Route::get('blacklist', [BlacklistController::class, 'index'])->name('blacklist.index');
    Route::post('blacklist', [BlacklistController::class, 'store'])->name('blacklist.store');
    Route::delete('blacklist/{blacklistWord}', [BlacklistController::class, 'destroy'])->name('blacklist.destroy');
});