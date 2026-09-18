<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\RateLimit;

/**
 * Default {@see Clock} implementation backed by the real system clock.
 */
final class SystemClock implements Clock
{
    public function now(): float
    {
        return microtime(true);
    }

    public function sleep(float $seconds): void
    {
        if ($seconds <= 0.0) {
            return;
        }

        usleep((int) ceil($seconds * 1_000_000));
    }
}
