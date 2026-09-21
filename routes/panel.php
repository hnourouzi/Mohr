<?php

use App\Http\Controllers\Web\ContentController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('panel')->name('panel.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('contents', [ContentController::class, 'index'])->name('contents.index');
    Route::get('contents/create', [ContentController::class, 'create'])->name('contents.create');
    Route::post('contents', [ContentController::class, 'store'])->name('contents.store');
    Route::get('contents/{content}', [ContentController::class, 'show'])->name('contents.show');
    Route::post('contents/{content}/retry', [ContentController::class, 'retry'])->name('contents.retry');

    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('wallet/top-up', [WalletController::class, 'topUp'])->name('wallet.topup');
});