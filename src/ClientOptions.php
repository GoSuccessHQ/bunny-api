<?php

declare(strict_types=1);

namespace GoSuccess\Bunny;

use InvalidArgumentException;

/**
 * Immutable transport and retry settings shared by all API clients.
 *
 * Credentials are not part of the options: every API client receives its own
 * key (account API key, storage zone password or Stream library key).
 */
final readonly class ClientOptions
{
    public const string DEFAULT_USER_AGENT = 'gosuccess/bunny-api (+https://github.com/GoSuccessHQ/bunny-api)';

    /**
     * @param float  $timeout         Maximum duration of a regular API request in seconds.
     * @param float  $connectTimeout  Maximum duration of the connection phase in seconds.
     * @param float  $transferTimeout Maximum duration of a streamed upload or download in
     *                                seconds (Edge Storage files, videos). `0.0` means no
     *                                limit; a transfer that stalls for 60 seconds is still
     *                                aborted.
     * @param int    $maxRetries      How often a failed request is retried. A `429` is
     *                                always retried; server errors and network failures only
     *                                for idempotent requests (GET, PUT, DELETE).
     * @param float  $retryBaseDelay  Base delay in seconds of the exponential backoff.
     * @param float  $maxRetryDelay   Upper bound in seconds for a single wait, which also
     *                                caps a server-provided `Retry-After`.
     * @param string $userAgent       Value of the `User-Agent` header.
     */
    public function __construct(
        public float $timeout = 30.0,
        public float $connectTimeout = 10.0,
        public float $transferTimeout = 0.0,
        public int $maxRetries = 3,
        public float $retryBaseDelay = 1.0,
        public float $maxRetryDelay = 60.0,
        public string $userAgent = self::DEFAULT_USER_AGENT,
    ) {
        foreach (['timeout' => $timeout, 'connectTimeout' => $connectTimeout, 'transferTimeout' => $transferTimeout, 'retryBaseDelay' => $retryBaseDelay, 'maxRetryDelay' => $maxRetryDelay] as $name => $value) {
            if ($value < 0.0) {
                throw new InvalidArgumentException("{$name} must not be negative.");
            }
        }

        if ($maxRetries < 0) {
            throw new InvalidArgumentException('maxRetries must not be negative.');
        }

        if (trim($userAgent) === '') {
            throw new InvalidArgumentException('userAgent must not be empty.');
        }
    }
}
