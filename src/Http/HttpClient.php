<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Http;

use GoSuccess\Bunny\Exception\TransportException;

/**
 * Minimal HTTP transport abstraction.
 *
 * The library ships with {@see CurlHttpClient} (no Composer dependencies). Provide
 * your own implementation to route requests through an existing HTTP stack, e.g.
 * Guzzle, without the library depending on it.
 */
interface HttpClient
{
    /**
     * Send a request and return the response.
     *
     * Implementations MUST:
     * - throw a {@see TransportException} for transport-level failures (connection
     *   errors, timeouts, …), but never for HTTP error status codes, which are
     *   returned as a normal {@see Response};
     * - read a {@see Stream} body from its current position and send
     *   {@see Stream::$size} as `Content-Length` when it is known;
     * - write the body of a 2xx response to {@see Request::$sink} when one is set,
     *   and keep the body of any other response in {@see Response::$body};
     * - not follow redirects.
     *
     * @throws TransportException
     */
    public function send(Request $request): Response;
}
