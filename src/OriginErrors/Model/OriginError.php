<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\OriginErrors\Model;

use DateTimeImmutable;
use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Model\ResponseModel;

/**
 * A request the CDN could not complete because the origin failed.
 *
 * The API sends the details as a JSON string inside a log entry; they are
 * decoded into the typed properties, and {@see $details} keeps all of them.
 */
final readonly class OriginError implements ResponseModel
{
    /**
     * @param string|null             $id         The unique ID of the log entry.
     * @param DateTimeImmutable|null  $timestamp  When the error occurred (UTC, millisecond precision).
     * @param string|null             $errorCode  What failed, e.g. `dns_lookup`, `http_timeout`
     *                                            (the CDN waits 60 seconds), `http_request_failure`,
     *                                            `network_socket_exception` or `http_loop_detected`.
     * @param int|null                $statusCode The status code the CDN answered with, e.g. 502 or 504.
     * @param string|null             $serverZone The edge region that handled the request, e.g. `DE`.
     * @param string|null             $requestUrl The requested path.
     * @param int|null                $pullZoneId The pull zone the request was made to.
     * @param string|null             $message    A human-readable description.
     * @param array<array-key, mixed> $details    Every field of the decoded log entry.
     */
    public function __construct(
        public ?string $id = null,
        public ?DateTimeImmutable $timestamp = null,
        public ?string $errorCode = null,
        public ?int $statusCode = null,
        public ?string $serverZone = null,
        public ?string $requestUrl = null,
        public ?int $pullZoneId = null,
        public ?string $message = null,
        public array $details = [],
    ) {}

    public static function fromArray(array $data): static
    {
        $labels = Cast::object($data['labels'] ?? null) ?? [];
        $details = self::decodeLog($data['log'] ?? null);

        return new self(
            id: Cast::string($data['logId'] ?? null),
            timestamp: Cast::timestampMs($data['timestamp'] ?? null),
            errorCode: Cast::string($labels['ErrorCode'] ?? null) ?? Cast::string($details['ErrorCode'] ?? null),
            statusCode: Cast::int($labels['StatusCode'] ?? null) ?? Cast::int($details['StatusCode'] ?? null),
            serverZone: Cast::string($labels['ServerZone'] ?? null),
            requestUrl: Cast::string($details['RequestUrl'] ?? null),
            pullZoneId: Cast::int($details['PullZoneId'] ?? null),
            message: Cast::string($details['Message'] ?? null),
            details: $details,
        );
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function decodeLog(mixed $log): array
    {
        if (\is_array($log)) {
            return $log;
        }

        if (!\is_string($log) || $log === '') {
            return [];
        }

        $decoded = json_decode($log, true);

        return \is_array($decoded) ? $decoded : [];
    }
}
