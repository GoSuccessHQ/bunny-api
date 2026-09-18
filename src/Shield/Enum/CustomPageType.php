<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Shield\Enum;

/**
 * The custom pages a Shield zone can show instead of bunny.net's own.
 */
enum CustomPageType: string
{
    /** Shown to requests blocked by the WAF, an access list or bot detection. */
    case Block = 'block';

    /** Shown to challenged requests. */
    case Challenge = 'challenge';

    /** Shown to requests over a rate limit. */
    case RateLimit = 'ratelimit';
}
