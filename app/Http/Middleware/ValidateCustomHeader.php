<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCustomHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasHeader('X-API-KEY') || $request->header('X-API-KEY') !== config('services.dismac.secret_key')) {
            return response()->json(['message' => 'Unauthorized: Invalid or missing API Key'], 401);
        }

        return $next($request);
    }
}
