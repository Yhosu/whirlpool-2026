<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application with guaranteed SQLite in-memory configuration.
     */
    public function createApplication()
    {
        putenv('DB_CONNECTION=sqlite');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_DATABASE'] = ':memory:';
        $_SERVER['DB_DATABASE'] = ':memory:';

        $app = require Application::inferBasePath().'/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');

        return $app;
    }

    /**
     * Safety check before any test runs.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $currentDb = config('database.default');
        if ($currentDb !== 'sqlite') {
            throw new \RuntimeException("CRITICAL SAFETY BLOCK: Tests must NEVER run on database '{$currentDb}'. Tests are restricted exclusively to SQLite in-memory (:memory:).");
        }
    }
}
