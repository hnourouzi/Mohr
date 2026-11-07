<?php

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\ForceJsonResponse;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(
            prepend: [
                ForceJsonResponse::class,
            ],
            remove: [
                EnsureFrontendRequestsAreStateful::class,
            ],
        );

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (InsufficientBalanceException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error($e->getMessage(), 402, 'insufficient_balance');
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['balance' => $e->getMessage()]);
        });

        $exceptions->render(function (InvalidStatusTransitionException $e, Request $request) {
            if ($request->is('api/*')) {
                $code = $request->routeIs('api.v1.contents.retry') ? 'not_retryable' : 'invalid_status_transition';

                return ApiResponse::error($e->getMessage(), 409, $code);
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 409);
            }

            return back()->withErrors(['status' => $e->getMessage()]);
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    $e->getMessage() ?: 'داده‌های ارسالی نامعتبر است.',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'validation_error',
                    $e->errors(),
                );
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('احراز هویت نشده است.', 401, 'unauthenticated');
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error($e->getMessage() ?: 'دسترسی غیرمجاز.', 403, 'forbidden');
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('منبع یافت نشد.', 404, 'not_found');
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return match ($e->getStatusCode()) {
                403 => ApiResponse::error($e->getMessage() ?: 'دسترسی غیرمجاز.', 403, 'forbidden'),
                429 => ApiResponse::error(
                    $e->getMessage() ?: 'تعداد درخواست‌ها بیش از حد مجاز است.',
                    429,
                    'too_many_requests',
                ),
                default => null,
            };
        });
    })->create();
