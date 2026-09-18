<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\RateLimit;

/**
 * Abstraction over the system clock so that rate limiting can be tested
 * deterministically without real wall-clock delays.
 */
interface Clock
{
    /**
     * Current time in seconds as a float (monotonic-ish, like microtime(true)).
     */
    public function now(): float;

    /**
     * Block the current execution for the given number of seconds.
     */
    public function sleep(float $seconds): void;
}
