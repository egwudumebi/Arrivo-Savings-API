<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestIdMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) ($request->headers->get('X-Request-Id') ?? Str::uuid());

        $request->headers->set('X-Request-Id', $requestId);

        Log::withContext([
            'request_id' => $requestId,
            'path' => $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
        ]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}
