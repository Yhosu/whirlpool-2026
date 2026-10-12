<?php

namespace Tests\Feature;

use App\Mail\ServerErrorMail;
use App\Services\ErrorNotificationService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ServerErrorNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_500_error_sends_email_to_josuandroidg7(): void
    {
        Mail::fake();

        Route::get('/test-server-error-trigger', function () {
            throw new Exception('Simulated 500 database failure on checkout');
        });

        $response = $this->get('/test-server-error-trigger');

        $response->assertStatus(500);

        Mail::assertSent(ServerErrorMail::class, function ($mail) {
            return $mail->hasTo('josuandroidg7@gmail.com')
                && str_contains($mail->errorData['message'], 'Simulated 500 database failure')
                && ! empty($mail->errorData['file'])
                && ! empty($mail->errorData['line'])
                && $mail->errorData['url'] !== '';
        });
    }

    public function test_404_error_does_not_send_server_error_email(): void
    {
        Mail::fake();

        $response = $this->get('/non-existent-route-for-testing-404');

        $response->assertStatus(404);

        Mail::assertNothingSent();
    }

    public function test_client_validation_error_does_not_send_email(): void
    {
        Mail::fake();

        $exception = ValidationException::withMessages(['email' => ['El email es requerido']]);
        ErrorNotificationService::notify($exception);

        Mail::assertNothingSent();
    }

    public function test_repeated_error_is_throttled_to_prevent_spam(): void
    {
        Mail::fake();

        $exception = new Exception('Repeated fatal crash');

        // First execution -> Sent
        ErrorNotificationService::notify($exception);
        Mail::assertSent(ServerErrorMail::class, 1);

        // Second execution immediately -> Ignored by throttle
        ErrorNotificationService::notify($exception);
        Mail::assertSent(ServerErrorMail::class, 1);
    }

    public function test_email_rendering_contains_key_error_details(): void
    {
        $payload = [
            'message' => 'QueryException: Table whirlpool.fake_table does not exist',
            'message_short' => 'QueryException: Table whirlpool.fake_table does not exist',
            'class' => 'Illuminate\Database\QueryException',
            'file' => 'app/Http/Controllers/TestController.php',
            'line' => 42,
            'url' => 'https://promowhirlpool.com/api/test',
            'method' => 'POST',
            'ip' => '192.168.1.1',
            'user_agent' => 'Mozilla/5.0 Chrome',
            'inputs' => ['foo' => 'bar'],
            'user' => ['id' => 1, 'name' => 'Admin User', 'email' => 'admin@whirlpool.com', 'role' => 'admin'],
            'trace' => "#0 app/Http/Controllers/TestController.php(42): doSomething()\n#1 vendor/laravel/framework/src/...",
            'occurred_at' => '2026-10-12 19:30:00 UTC',
            'env' => 'production',
        ];

        $mailable = new ServerErrorMail($payload);

        $rendered = $mailable->render();

        $this->assertStringContainsString('QueryException: Table whirlpool.fake_table does not exist', $rendered);
        $this->assertStringContainsString('app/Http/Controllers/TestController.php', $rendered);
        $this->assertStringContainsString('Línea 42', $rendered);
        $this->assertStringContainsString('https://promowhirlpool.com/api/test', $rendered);
        $this->assertStringContainsString('Admin User', $rendered);
        $this->assertStringContainsString('admin@whirlpool.com', $rendered);
    }
}
