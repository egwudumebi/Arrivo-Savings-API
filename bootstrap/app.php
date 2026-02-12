<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [
            App\Http\Middleware\RejectStatefulRequests::class,
            App\Http\Middleware\RequestIdMiddleware::class,
            App\Http\Middleware\ApiSecurityHeaders::class,
            App\Http\Middleware\EnsureNotSuspended::class,
        ]);

        $middleware->alias([
            'user' => App\Http\Middleware\UserMiddleware::class,
            'admin' => App\Http\Middleware\AdminMiddleware::class,
            'super_admin' => App\Http\Middleware\SuperAdminMiddleware::class,
            'role' => App\Http\Middleware\RoleMiddleware::class,
            'not_suspended' => App\Http\Middleware\EnsureNotSuspended::class,
            'request_id' => App\Http\Middleware\RequestIdMiddleware::class,
            'security_headers' => App\Http\Middleware\ApiSecurityHeaders::class,
            'reject_stateful' => App\Http\Middleware\RejectStatefulRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return null;
            }

            $status = 500;
            $code = 'server_error';
            $message = 'Server Error.';
            $errors = null;

            if ($e instanceof ValidationException) {
                $status = 422;
                $code = 'validation_error';
                $message = 'The given data was invalid.';
                $errors = $e->errors();
            } elseif ($e instanceof AuthenticationException) {
                $status = 401;
                $code = 'unauthenticated';
                $message = 'Unauthenticated.';
            } elseif ($e instanceof AuthorizationException) {
                $status = 403;
                $code = 'forbidden';
                $message = $e->getMessage() !== '' ? $e->getMessage() : 'Forbidden.';
            } elseif ($e instanceof NotFoundHttpException) {
                $status = 404;
                $code = 'not_found';
                $message = 'Not Found.';
            } elseif ($e instanceof MethodNotAllowedHttpException) {
                $status = 405;
                $code = 'method_not_allowed';
                $message = 'Method Not Allowed.';
            } elseif ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $code = $status >= 500 ? 'server_error' : 'http_error';
                $message = $e->getMessage() !== '' ? $e->getMessage() : ($status >= 500 ? 'Server Error.' : 'Request Error.');
            }

            $requestId = (string) ($request->headers->get('X-Request-Id') ?? '');

            if ($status >= 500) {
                Log::error('Unhandled exception', [
                    'request_id' => $requestId,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]);

                if (! config('app.debug')) {
                    $message = 'Server Error.';
                }
            }

            $payload = [
                'message' => $message,
                'error_code' => $code,
                'request_id' => $requestId,
            ];

            if ($errors !== null) {
                $payload['errors'] = $errors;
            }

            return response()->json($payload, $status);
        });
    })->create();
