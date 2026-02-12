<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('api');

        if ($user === null) {
            return $next($request);
        }

        if ($user instanceof Model) {
            $user = $user->fresh() ?? $user;
        }

        if ($user->suspended_at !== null) {
            return response()->json(['message' => 'Account suspended.'], 403);
        }

        return $next($request);
    }
}
