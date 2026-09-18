<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Http;

/**
 * Immutable HTTP request passed to an {@see HttpClient}.
 */
final readonly class Request
{
    /**
     * @param non-empty-string      $uri     Absolute request URI.
     * @param array<string, string> $headers Map of header name => value.
     * @param string|Stream|null    $body    Request body, either in memory or streamed.
     * @param Stream|null           $sink    When set, the body of a successful (2xx)
     *                                       response is written to this stream instead
     *                                       of {@see Response::$body}. Error bodies are
     *                                       always kept in memory.
     * @param float|null            $timeout Maximum duration of the whole request in
     *                                       seconds; `0.0` disables the limit and
     *                                       `null` uses the transport's default.
     */
    public function __construct(
        public Method $method,
        public string $uri,
        public array $headers = [],
        public string|Stream|null $body = null,
        public ?Stream $sink = null,
        public ?float $timeout = null,
    ) {}
}
