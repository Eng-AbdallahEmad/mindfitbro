<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Guard against RefreshDatabase running (migrating + per-test transaction
     * wrapping, but critically: this is also where a *misconfigured* setup
     * could point at a real database) against anything but a dedicated test
     * database. Runs BEFORE parent::setUp() triggers RefreshDatabase's own
     * setUp, by booting the application here first so config() is available,
     * then letting parent::setUp() see $this->app already set and skip
     * re-creating it. See docs/testing.md.
     */
    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->guardAgainstNonTestDatabase();

        parent::setUp();
    }

    private function guardAgainstNonTestDatabase(): void
    {
        $connection = config('database.default');
        $database   = config("database.connections.{$connection}.database");

        if (!str_ends_with((string) $database, '_test')) {
            throw new \RuntimeException(
                "Refusing to run tests against database '{$database}': it does not end ".
                "in '_test'. RefreshDatabase migrates and wraps every test in a ".
                "transaction on the active connection — a misconfigured .env/phpunit.xml ".
                "pointing at the dev or production database would be destructive. Point ".
                "DB_DATABASE (phpunit.xml) at a dedicated database such as mindfitbro_test."
            );
        }
    }
}
