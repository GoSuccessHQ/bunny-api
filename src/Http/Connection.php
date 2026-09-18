<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Http;

use GoSuccess\Bunny\ClientOptions;
use GoSuccess\Bunny\Exception\ApiException;
use GoSuccess\Bunny\Exception\RateLimitException;
use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Exception\TransportException;
use GoSuccess\Bunny\RateLimit\Clock;
use GoSuccess\Bunny\RateLimit\RateLimiter;
use GoSuccess\Bunny\RateLimit\SystemClock;
use InvalidArgumentException;
use JsonException;
use SensitiveParameter;

/**
 * Mid-level HTTP layer of one API: builds authenticated requests, sends them
 * through the {@see HttpClient}, applies the rate limit, retries transient
 * failures and maps error responses to typed exceptions.
 *
 * @internal
 */
final class Connection
{
    private const int JSON_ENCODE_FLAGS = \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_PRESERVE_ZERO_FRACTION;

    /**
     * Base URI without a trailing slash, e.g. `https://api.bunny.net`.
     */
    public readonly string $baseUri;

    /**
     * @param string $accessKey Value of the `AccessKey` header.
     */
    public function __construct(
        string $baseUri,
        #[SensitiveParameter]
        private readonly string $accessKey,
        private readonly ClientOptions $options,
        private readonly HttpClient $httpClient,
        private readonly RateLimiter $rateLimiter,
        private readonly Clock $clock = new SystemClock(),
    ) {
        $baseUri = rtrim($baseUri, '/');

        if (!preg_match('~^https?://[^/?#]+~i', $baseUri)) {
            throw new InvalidArgumentException("The base URI must be an absolute http(s) URI, got \"{$baseUri}\".");
        }

        if (trim($accessKey) === '') {
            throw new InvalidArgumentException('The access key must not be empty.');
        }

        $this->baseUri = $baseUri;
    }

    /**
     * Send a request with an optional JSON object body and decode the JSON response.
     *
     * @param array<string, mixed>         $query
     * @param array<array-key, mixed>|null $body  Encoded as a JSON object; an empty
     *                                            array becomes `{}`.
     *
     * @return mixed The decoded response, or null for an empty body.
     */
    public function json(Method $method, string $path, array $query = [], ?array $body = null): mixed
    {
        $headers = ['Accept' => 'application/json'];
        $payload = null;

        if ($body !== null) {
            $headers['Content-Type'] = 'application/json';
            $payload = self::encodeJson($body);
        }

        return self::decodeJson($this->send($method, $path, $query, $payload, $headers)->body);
    }

    /**
     * Send a request and return the raw response (e.g. for text or binary bodies).
     *
     * A {@see Stream} body or a sink marks the request as a transfer, which is
     * subject to {@see ClientOptions::$transferTimeout} instead of the regular
     * timeout.
     *
     * @param array<string, mixed>  $query
     * @param array<string, string> $headers Extra headers, overriding the defaults.
     *
     * @throws ApiException       On an error response once all retries are used up.
     * @throws TransportException On a network failure once all retries are used up.
     */
    public function send(
        Method $method,
        string $path,
        array $query = [],
        string|Stream|null $body = null,
        array $headers = [],
        ?Stream $sink = null,
    ): Response {
        $isTransfer = $body instanceof Stream || $sink !== null;
        $request = new Request(
            method: $method,
            uri: $this->buildUri($path, $query),
            headers: [
                'AccessKey' => $this->accessKey,
                'Accept' => 'application/json',
                'User-Agent' => $this->options->userAgent,
                ...$headers,
            ],
            body: $body,
            sink: $sink,
            timeout: $isTransfer ? $this->options->transferTimeout : $this->options->timeout,
        );

        $bodyStart = $body instanceof Stream ? $body->position() : null;
        $sinkStart = $sink?->position();
        $attempt = 0;

        while (true) {
            $this->rateLimiter->acquire();

            try {
                $response = $this->httpClient->send($request);
            } catch (TransportException $e) {
                // The request may have reached the server, so only repeat it if
                // doing so cannot duplicate a write.
                if ($method->isIdempotent() && $attempt < $this->options->maxRetries && $this->rewind($request, $bodyStart, $sinkStart)) {
                    $this->clock->sleep($this->backoffDelay($attempt++));

                    continue;
                }

                throw $e;
            }

            if ($response->isSuccessful) {
                return $response;
            }

            if ($this->isRetryable($response->statusCode, $method) && $attempt < $this->options->maxRetries && $this->rewind($request, $bodyStart, $sinkStart)) {
                $this->clock->sleep($this->retryDelay($response, $attempt++));

                continue;
            }

            throw ApiException::fromResponse($response, $request);
        }
    }

    /**
     * @param array<array-key, mixed> $body
     */
    public static function encodeJson(array $body): string
    {
        if ($body === []) {
            return '{}';
        }

        try {
            return json_encode($body, self::JSON_ENCODE_FLAGS);
        } catch (JsonException $e) {
            throw new SerializationException("Failed to encode the request body as JSON: {$e->getMessage()}", 0, $e);
        }
    }

    public static function decodeJson(string $body): mixed
    {
        if (trim($body) === '') {
            return null;
        }

        try {
            return json_decode($body, true, 512, \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new SerializationException("Failed to decode the JSON response: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Hide the access key from var_dump() and print_r().
     *
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return ['baseUri' => $this->baseUri, 'accessKey' => '********'];
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return non-empty-string
     */
    private function buildUri(string $path, array $query): string
    {
        $uri = "{$this->baseUri}/" . ltrim($path, '/');
        $queryString = Query::build($query);

        return $queryString === '' ? $uri : "{$uri}?{$queryString}";
    }

    /**
     * Reset streamed bodies and sinks before a retry. Returns false if a stream
     * cannot be rewound, in which case the request must not be repeated.
     *
     * @param int<0, max>|null $bodyStart
     * @param int<0, max>|null $sinkStart
     */
    private function rewind(Request $request, ?int $bodyStart, ?int $sinkStart): bool
    {
        if ($request->body instanceof Stream) {
            if ($bodyStart === null || !$request->body->isSeekable || fseek($request->body->resource, $bodyStart) !== 0) {
                return false;
            }
        }

        if ($request->sink !== null) {
            $resource = $request->sink->resource;

            if ($sinkStart === null || !$request->sink->isSeekable || !ftruncate($resource, $sinkStart) || fseek($resource, $sinkStart) !== 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * A `429` is always safe to retry (the request was rejected, not processed).
     * A `5xx` may have been processed, so it is only retried for idempotent methods.
     */
    private function isRetryable(int $status, Method $method): bool
    {
        return $status === 429 || ($status >= 500 && $method->isIdempotent());
    }

    private function retryDelay(Response $response, int $attempt): float
    {
        if ($response->statusCode === 429) {
            $retryAfter = RateLimitException::parseRetryAfter($response->header('retry-after'));

            if ($retryAfter !== null) {
                return min((float) $retryAfter, $this->options->maxRetryDelay);
            }
        }

        return $this->backoffDelay($attempt);
    }

    private function backoffDelay(int $attempt): float
    {
        return min($this->options->retryBaseDelay * (2 ** $attempt), $this->options->maxRetryDelay);
    }
}
