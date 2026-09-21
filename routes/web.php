<?php

use App\Http\Controllers\Web\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Web\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Prompts\Moderation\ContentModerationPrompt;

Route::get('/', fn() => view('welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


Route::get('test', function () {
    $response = Http::withToken(config('services.openrouter.api_key'))
        ->acceptJson()
        ->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openrouter/free',

            'messages' => [
                [
                    'role' => 'system',
                    'content' => ContentModerationPrompt::system(),
                ],
                [
                    'role' => 'user',
                    'content' => 'کشتن معشوقه خود به بدترین شکل ممکن.',
                ]
            ],
        ]);
        if ($response->failed()) {
            dd($response->status(), $response->body());
        }
    dd(
        $response->status(),
        json_decode($response->json()['choices'][0]['message']['content'], true)['flagged']
    );
});

require __DIR__ . '/panel.php';
require __DIR__ . '/admin.php';