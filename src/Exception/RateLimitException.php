<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Exception;

/**
 * The request was rejected because a rate limit was exceeded (HTTP 429).
 *
 * The client already retries such requests automatically; this exception is
 * only thrown once all retries are used up. {@see $retryAfter} holds the number
 * of seconds to wait when the API sent a `Retry-After` header.
 */
final class RateLimitException extends ApiException
{
    public function __construct(
        string $message,
        int $statusCode,
        string $responseBody = '',
        ?string $errorKey = null,
        ?string $field = null,
        ?string $requestId = null,
        public readonly ?int $retryAfter = null,
    ) {
        parent::__construct($message, $statusCode, $responseBody, $errorKey, $field, $requestId);
    }

    /**
     * Parse a `Retry-After` header value into seconds.
     *
     * Supports both the delay-seconds form ("30") and the HTTP-date form
     * ("Wed, 21 Oct 2026 07:28:00 GMT"). Returns null if the value is empty or
     * cannot be interpreted.
     */
    public static function parseRetryAfter(string $value): ?int
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (ctype_digit($value)) {
            return (int) $value;
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return null;
        }

        return max(0, $timestamp - time());
    }
}
