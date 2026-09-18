<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Support;

use Closure;
use GoSuccess\Bunny\Exception\TransportException;
use GoSuccess\Bunny\Http\HttpClient;
use GoSuccess\Bunny\Http\Request;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use RuntimeException;

/**
 * Test double that returns a queue of predefined responses (or throws predefined
 * exceptions) and records every request it received.
 */
final class MockHttpClient implements HttpClient
{
    /** @var list<Response|TransportException> */
    private array $queue;

    /** @var list<Request> */
    public array $requests = [];

    /**
     * Request bodies as seen at send time; streams are read without being consumed.
     *
     * @var list<string|null>
     */
    public array $bodies = [];

    /**
     * Called for every request before the queued outcome is returned, e.g. to
     * simulate a transport that consumes the body or writes to the sink.
     *
     * @var (Closure(Request): void)|null
     */
    public ?Closure $onSend = null;

    public function __construct(Response|TransportException ...$queue)
    {
        $this->queue = array_values($queue);
    }

    public function send(Request $request): Response
    {
        $this->requests[] = $request;
        $this->bodies[] = $this->peek($request);

        if ($this->onSend !== null) {
            ($this->onSend)($request);
        }

        $next = array_shift($this->queue);

        if ($next === null) {
            throw new RuntimeException('MockHttpClient ran out of queued responses.');
        }

        if ($next instanceof TransportException) {
            throw $next;
        }

        // Like a real transport: a successful body goes to the sink, if any.
        if ($request->sink !== null && $next->isSuccessful) {
            fwrite($request->sink->resource, $next->body);

            return new Response($next->statusCode, '', $next->headers, $next->reasonPhrase);
        }

        return $next;
    }

    public function callCount(): int
    {
        return \count($this->requests);
    }

    /**
     * Decode the JSON body of the request at the given index.
     *
     * @return array<array-key, mixed>
     */
    public function jsonBody(int $index = 0): array
    {
        $body = $this->bodies[$index] ?? null;
        $decoded = \is_string($body) ? json_decode($body, true) : null;

        if (!\is_array($decoded)) {
            throw new RuntimeException("Request {$index} has no JSON object body.");
        }

        return $decoded;
    }

    private function peek(Request $request): ?string
    {
        $body = $request->body;

        if (!$body instanceof Stream) {
            return $body;
        }

        // Like a real transport: a stream with a size sends exactly that many bytes.
        $position = $body->position();
        $contents = $body->size === null ? $body->contents() : (string) stream_get_contents($body->resource, $body->size);

        if ($position !== null && $body->isSeekable) {
            fseek($body->resource, $position);
        }

        return $contents;
    }
}
