<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\OriginErrors;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use GoSuccess\Bunny\ClientOptions;
use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Http\Connection;
use GoSuccess\Bunny\Http\CurlHttpClient;
use GoSuccess\Bunny\Http\HttpClient;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\OriginErrors\Model\OriginErrorLog;
use GoSuccess\Bunny\RateLimit\NullRateLimiter;
use GoSuccess\Bunny\RateLimit\RateLimiter;
use SensitiveParameter;

/**
 * Client for the bunny.net Origin Errors API (`https://cdn-origin-logging.bunny.net`).
 *
 * Reports the requests the CDN could not complete because the origin failed:
 * DNS failures, timeouts, connection errors and the like.
 */
final class OriginErrorsClient
{
    public const string DEFAULT_BASE_URI = 'https://cdn-origin-logging.bunny.net';

    private readonly Connection $connection;

    /**
     * @param string          $apiKey      The account API key (bunny.net dashboard → Account settings → API key).
     * @param ClientOptions   $options     Timeouts, retries and user agent.
     * @param HttpClient|null $httpClient  Custom transport; defaults to the built-in cURL transport.
     * @param RateLimiter     $rateLimiter Client-side throttling; disabled by default.
     * @param string          $baseUri     Base URI of the API.
     */
    public function __construct(
        #[SensitiveParameter]
        string $apiKey,
        ClientOptions $options = new ClientOptions(),
        ?HttpClient $httpClient = null,
        RateLimiter $rateLimiter = new NullRateLimiter(),
        string $baseUri = self::DEFAULT_BASE_URI,
    ) {
        $this->connection = new Connection(
            $baseUri,
            $apiKey,
            $options,
            $httpClient ?? new CurlHttpClient($options->timeout, $options->connectTimeout),
            $rateLimiter,
        );
    }

    /**
     * Get the origin errors of a pull zone on one day.
     *
     * `GET /{pullZoneId}/{dateTime}`
     *
     * @param int               $pullZoneId The ID of the pull zone.
     * @param DateTimeInterface $date       The day, taken in UTC; only recent days are retained.
     */
    public function get(int $pullZoneId, DateTimeInterface $date): OriginErrorLog
    {
        $day = DateTimeImmutable::createFromInterface($date)->setTimezone(new DateTimeZone('UTC'))->format('m-d-Y');
        $data = $this->connection->json(Method::Get, "{$pullZoneId}/{$day}");

        if (!\is_array($data)) {
            $type = get_debug_type($data);

            throw new SerializationException("Expected a JSON object, got {$type}.");
        }

        return OriginErrorLog::fromArray($data);
    }
}
