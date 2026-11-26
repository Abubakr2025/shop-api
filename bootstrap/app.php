<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => null);

        $middleware->api(append: [
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // 1. Ошибка авторизации (нет токена) -> 401
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(['message' => 'Не авторизован. Требуется токен.'], 401);
        });

        // 2. Отказ в доступе (нет прав) -> 403
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 403);
        });

        // 3. Ошибки валидации -> 422
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json(['message' => 'Ошибка валидации', 'errors' => $e->errors()], 422);
        });

        // 4. Модель не найдена -> 404
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            return response()->json(['message' => 'Ресурс не найден'], 404);
        });

        // 5. Слишком много запросов (Rate Limiting) -> 429
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            return response()->json([
                'message' => 'Слишком много попыток. Попробуйте позже.',
                'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
            ], 429);
        });

        // 5.5 Бизнес-ошибки (нехватка товара) -> 409 Conflict
        $exceptions->render(function (\App\Exceptions\DomainException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        });

        // 6. Остальные ошибки сервера -> 500 (с логированием)
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                \Illuminate\Support\Facades\Log::error('Ошибка сервера: ' . $e->getMessage(), [
                    'url' => $request->url(),
                    'user_id' => $request->user()?->id,
                    'exception' => $e,
                ]);
                
                return response()->json(['message' => 'Внутренняя ошибка сервера'], 500);
            }
        });
    })->create();
