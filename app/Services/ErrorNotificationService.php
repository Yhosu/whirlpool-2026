<?php

namespace App\Services;

use App\Mail\ServerErrorMail;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ErrorNotificationService
{
    /**
     * Correo principal de notificación de errores del servidor.
     */
    public const DEFAULT_RECIPIENT = 'josuandroidg7@gmail.com';

    /**
     * Procesa la excepción y envía el correo si corresponde a un error 500 / inesperado.
     */
    public static function notify(Throwable $exception): void
    {
        // Solo notificar errores en entorno de producción. NUNCA en local ni en testing.
        if (! app()->environment('production')) {
            return;
        }

        // 1. Filtrar excepciones del lado del cliente o normales de flujo HTTP (< 500)
        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
            return;
        }

        if (
            $exception instanceof ValidationException ||
            $exception instanceof AuthenticationException ||
            $exception instanceof TokenMismatchException
        ) {
            return;
        }

        // 2. Prevenir tormentas de spam (Rate limit de 5 minutos para el mismo error exacto)
        try {
            $errorSignature = md5(get_class($exception).':'.$exception->getMessage().':'.$exception->getFile().':'.$exception->getLine());
            $throttleKey = 'error_500_alert_'.$errorSignature;

            if (cache()->has($throttleKey)) {
                return;
            }

            cache()->put($throttleKey, true, now()->addMinutes(5));
        } catch (Throwable) {
            // Si el driver de cache falla (ej. caída de base de datos), proseguir con el envío
        }

        // 3. Ensamblar datos del error y contexto de la petición
        try {
            $payload = static::buildPayload($exception);
            $recipient = env('ERROR_NOTIFICATION_EMAIL', self::DEFAULT_RECIPIENT);

            // Respetar el mailer configurado en la aplicación (ej. log en local, resend en prod)
            $mailer = config('mail.default');

            Mail::mailer($mailer)->to($recipient)->send(new ServerErrorMail($payload));
        } catch (Throwable $mailException) {
            // Fallback seguro: Si el envío falla (ej. sin conexión a Resend), no romper la app
            Log::error('Fallo al enviar notificación de Error 500 por correo: '.$mailException->getMessage(), [
                'original_error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        }
    }

    /**
     * Construye la estructura de datos que se presentará en el correo.
     *
     * @return array<string, mixed>
     */
    protected static function buildPayload(Throwable $exception): array
    {
        $request = request();

        $url = app()->runningInConsole() ? 'Consola / CLI (Artisan)' : 'N/A';
        $method = 'N/A';
        $ip = 'N/A';
        $userAgent = 'N/A';
        $inputs = [];
        $user = null;

        if ($request && ! app()->runningInConsole()) {
            $url = $request->fullUrl();
            $method = $request->method();
            $ip = $request->ip() ?? 'N/A';
            $userAgent = $request->userAgent() ?? 'N/A';

            // Parámetros sin contraseñas ni datos sensibles
            $inputs = $request->except([
                'password',
                'password_confirmation',
                'current_password',
                'token',
                'secret',
                'credit_card',
                'cvv',
            ]);

            try {
                if (auth()->check()) {
                    $u = auth()->user();
                    $user = [
                        'id' => $u->id ?? null,
                        'name' => $u->name ?? null,
                        'email' => $u->email ?? null,
                        'role' => $u->role ?? null,
                    ];
                }
            } catch (Throwable) {
                // Si auth falla, no interferir
            }
        }

        $rawMessage = $exception->getMessage() ?: 'Error inesperado sin mensaje';
        $traceLines = explode("\n", $exception->getTraceAsString());
        $shortTrace = array_slice($traceLines, 0, 25);

        return [
            'message' => $rawMessage,
            'message_short' => Str::limit($rawMessage, 70),
            'class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'url' => $url,
            'method' => $method,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'inputs' => $inputs,
            'user' => $user,
            'trace' => implode("\n", $shortTrace),
            'occurred_at' => now()->format('Y-m-d H:i:s T'),
            'env' => config('app.env', 'production'),
        ];
    }
}
