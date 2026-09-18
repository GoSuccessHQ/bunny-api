<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\RateLimit;

/**
 * No-op rate limiter that never throttles.
 *
 * Useful for tests or when throttling is handled elsewhere (e.g. by an external
 * gateway or a distributed limiter wrapping the client).
 */
final class NullRateLimiter implements RateLimiter
{
    public function acquire(): void
    {
        // Intentionally does nothing.
    }
}
