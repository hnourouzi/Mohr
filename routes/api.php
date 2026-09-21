<?php

use App\Http\Controllers\Api\ContentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('contents', ContentController::class)->only(['index', 'store', 'show']);
});