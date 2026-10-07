<?php

namespace App\Http\Middleware;

use App\Services\ApiResponseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitByIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $maxAttempts  Número máximo de intentos permitidos en el período
     * @param  int  $decayMinutes  Período en minutos
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $ip = $request->ip() ?: '127.0.0.1';
        $key = 'rate_limit_ip:' . sha1($ip . '|' . $request->path());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);

            return ApiResponseService::error(
                message: 'Demasiadas solicitudes. Por favor, espere antes de intentar nuevamente.',
                errors: [
                    "Has excedido el límite permitido de solicitudes desde tu dirección IP ({$maxAttempts} por minuto).",
                    "Por favor, reintenta en {$retryAfter} segundos.",
                ],
                data: [
                    'retry_after' => $retryAfter,
                    'max_attempts' => $maxAttempts,
                ],
                others: [],
                code: 429
            );
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', (string) $maxAttempts);
        $remaining = max(0, $maxAttempts - RateLimiter::attempts($key));
        $response->headers->set('X-RateLimit-Remaining', (string) $remaining);

        return $response;
    }
}
