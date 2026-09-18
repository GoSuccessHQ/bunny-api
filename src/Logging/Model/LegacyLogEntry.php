<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Logging\Model;

use DateTimeImmutable;
use GoSuccess\Bunny\Model\Cast;

/**
 * One line of a legacy (v1) log file.
 *
 * The line format is pipe-separated; bunny.net strips pipes from the fields a
 * client controls (URL, referrer, user agent), so the split is unambiguous.
 */
final readonly class LegacyLogEntry
{
    /**
     * @param string|null            $cacheStatus         `HIT`, `MISS`, `BYPASS`, `REVALIDATED`, `STALE`,
     *                                                    `UPDATING`, or null for requests without a cache
     *                                                    interaction (logged as `-`).
     * @param int|null               $statusCode          The HTTP status code.
     * @param DateTimeImmutable|null $timestamp           When the edge received the request (UTC, milliseconds).
     * @param int|null               $bytesSent           Bytes sent to the client, headers included.
     * @param int|null               $pullZoneId          The pull zone that served the request.
     * @param string|null            $remoteIp            The client IP, anonymized unless disabled.
     * @param string|null            $referer             The Referer header.
     * @param string|null            $url                 The requested URL.
     * @param string|null            $edgeLocation        The POP code of the edge that served the request.
     * @param string|null            $userAgent           The User-Agent header.
     * @param string|null            $requestId           The unique request ID.
     * @param string|null            $countryCode         ISO 3166 alpha-2 code of the client's country.
     * @param int|null               $bodyBytesSent       Body bytes sent (extended logging only).
     * @param string|null            $rangeHeader         The Range header (extended logging only).
     * @param string|null            $authorizationHeader The Authorization header (extended logging only).
     */
    public function __construct(
        public ?string $cacheStatus = null,
        public ?int $statusCode = null,
        public ?DateTimeImmutable $timestamp = null,
        public ?int $bytesSent = null,
        public ?int $pullZoneId = null,
        public ?string $remoteIp = null,
        public ?string $referer = null,
        public ?string $url = null,
        public ?string $edgeLocation = null,
        public ?string $userAgent = null,
        public ?string $requestId = null,
        public ?string $countryCode = null,
        public ?int $bodyBytesSent = null,
        public ?string $rangeHeader = null,
        public ?string $authorizationHeader = null,
    ) {}

    /**
     * Parse one line of the log file.
     */
    public static function fromLine(string $line): self
    {
        $fields = explode('|', rtrim($line, "\r\n"));
        $field = static function (int $index) use ($fields): ?string {
            $value = $fields[$index] ?? null;

            // "-" marks an empty value.
            return $value === null || $value === '' || $value === '-' ? null : $value;
        };

        return new self(
            cacheStatus: $field(0),
            statusCode: Cast::int($field(1)),
            timestamp: Cast::timestampMs($field(2)),
            bytesSent: Cast::int($field(3)),
            pullZoneId: Cast::int($field(4)),
            remoteIp: $field(5),
            referer: $field(6),
            url: $field(7),
            edgeLocation: $field(8),
            userAgent: $field(9),
            requestId: $field(10),
            countryCode: $field(11),
            bodyBytesSent: Cast::int($field(12)),
            rangeHeader: $field(13),
            authorizationHeader: $field(14),
        );
    }
}
