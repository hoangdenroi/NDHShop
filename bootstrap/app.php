<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/app.php'));

            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Khi user chưa đăng nhập, flash toast rồi redirect về trang login
        $middleware->redirectGuestsTo(function () {
            session()->flash('toast_type', 'warning');
            session()->flash('toast_message', 'Bạn cần đăng nhập để sử dụng tính năng này!');
            return route('login');
        });

        $middleware->web(append: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':web',
        ]);

        // Thêm session/cookie vào API để hỗ trợ auth:web từ AJAX
        $middleware->api(prepend: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ]);

        // Bỏ qua kiểm tra CSRF cho các route API (bao gồm webhook từ SePay)
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Đăng ký alias để dùng middleware('role:admin') trong Route
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $statusCode = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface 
                    ? $e->getStatusCode() 
                    : 500;

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Lỗi hệ thống',
                    'statusCode' => $statusCode,
                    'data' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ] : null,
                ], $statusCode);
            }
        });
    })->create();
