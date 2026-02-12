<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RejectStatefulRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $cookieHeader = (string) $request->headers->get('cookie', '');

        if ($cookieHeader !== '' && str_contains($cookieHeader, 'laravel_session=')) {
            return response()->json([
                'message' => 'Stateful session cookies are not supported for this API. Use the Authorization header.',
                'error_code' => 'stateful_request_rejected',
            ], 400);
        }

        return $next($request);
    }
}
