<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Base class of the checks against the live API.
 *
 * These tests only ever READ: every request is free of side effects (GETs and
 * a few reads that use POST, such as availability checks and searches), so they
 * are safe to run against a production account. They are skipped unless the
 * BUNNY_API_KEY environment variable holds an account API key:
 *
 *   BUNNY_API_KEY=... composer test:integration
 */
abstract class IntegrationTestCase extends TestCase
{
    protected static function apiKey(): string
    {
        $key = getenv('BUNNY_API_KEY');

        if (!\is_string($key) || $key === '') {
            self::markTestSkipped('Set BUNNY_API_KEY to run the read-only integration tests.');
        }

        return $key;
    }
}
