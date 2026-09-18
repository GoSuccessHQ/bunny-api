<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Http;

use GoSuccess\Bunny\ClientOptions;
use GoSuccess\Bunny\Exception\ApiException;
use GoSuccess\Bunny\Exception\AuthenticationException;
use GoSuccess\Bunny\Exception\BadRequestException;
use GoSuccess\Bunny\Exception\ConflictException;
use GoSuccess\Bunny\Exception\ForbiddenException;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Exception\RateLimitException;
use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Exception\ServerException;
use GoSuccess\Bunny\Exception\TransportException;
use GoSuccess\Bunny\Exception\ValidationException;
use GoSuccess\Bunny\Http\Connection;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Tests\Support\FakeClock;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use GoSuccess\Bunny\Tests\Support\SpyRateLimiter;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Connection::class)]
final class ConnectionTest extends TestCase
{
    public function testSendsAuthenticatedJsonRequests(): void
    {
        $http = new MockHttpClient(new Response(200, '{"Id":1}'));

        $result = $this->connection($http)->json(Method::Post, '/pullzone', ['page' => 1, 'search' => null], ['Name' => 'zone']);

        self::assertSame(['Id' => 1], $result);
        $request = $http->requests[0];
        self::assertSame('https://api.bunny.net/pullzone?page=1', $request->uri);
        self::assertSame('secret-key', $request->headers['AccessKey']);
        self::assertSame('application/json', $request->headers['Content-Type']);
        self::assertSame('application/json', $request->headers['Accept']);
        self::assertSame(ClientOptions::DEFAULT_USER_AGENT, $request->headers['User-Agent']);
        self::assertSame('{"Name":"zone"}', $request->body);
        self::assertSame(30.0, $request->timeout);
    }

    public function testEncodesAnEmptyBodyAsJsonObject(): void
    {
        $http = new MockHttpClient(new Response(204));

        $result = $this->connection($http)->json(Method::Post, 'pullzone/1/purgeCache', body: []);

        self::assertNull($result);
        self::assertSame('{}', $http->requests[0]->body);
    }

    public function testStreamedTransfersUseTheTransferTimeout(): void
    {
        $http = new MockHttpClient(new Response(201));
        $connection = $this->connection($http, new ClientOptions(transferTimeout: 600.0));

        $connection->send(Method::Put, 'zone/file.bin', body: Stream::fromString('data'));

        self::assertSame(600.0, $http->requests[0]->timeout);
    }

    public function testRetries429AndHonorsRetryAfter(): void
    {
        $http = new MockHttpClient(
            new Response(429, '', ['retry-after' => '5']),
            new Response(200, '[]'),
        );
        $clock = new FakeClock();
        $rateLimiter = new SpyRateLimiter();

        $this->connection($http, clock: $clock, rateLimiter: $rateLimiter)->json(Method::Post, 'purge');

        self::assertSame(2, $http->callCount());
        self::assertSame(2, $rateLimiter->acquireCount);
        self::assertSame([5.0], $clock->sleeps);
    }

    public function testCapsAnExcessiveRetryAfter(): void
    {
        $http = new MockHttpClient(
            new Response(429, '', ['retry-after' => '99999']),
            new Response(200, '{}'),
        );
        $clock = new FakeClock();

        $this->connection($http, new ClientOptions(maxRetryDelay: 30.0), $clock)->json(Method::Get, 'pullzone');

        self::assertSame([30.0], $clock->sleeps);
    }

    public function testRetriesIdempotentRequestsOnServerErrorsWithBackoff(): void
    {
        $http = new MockHttpClient(new Response(502), new Response(503), new Response(200, '{}'));
        $clock = new FakeClock();

        $this->connection($http, clock: $clock)->json(Method::Get, 'pullzone');

        self::assertSame(3, $http->callCount());
        self::assertSame([1.0, 2.0], $clock->sleeps);
    }

    public function testDoesNotRetryPostOnServerErrors(): void
    {
        $http = new MockHttpClient(new Response(500, '{"Message":"boom"}'));

        try {
            $this->connection($http)->json(Method::Post, 'pullzone/1', body: ['OriginUrl' => 'https://example.com']);
            self::fail('Expected a ServerException.');
        } catch (ServerException $e) {
            self::assertSame(500, $e->statusCode);
        }

        self::assertSame(1, $http->callCount());
    }

    public function testRetriesTransportErrorsOnlyForIdempotentRequests(): void
    {
        $http = new MockHttpClient(new TransportException('reset'), new Response(200, '{}'));
        $this->connection($http, clock: new FakeClock())->json(Method::Delete, 'pullzone/1');
        self::assertSame(2, $http->callCount());

        $http = new MockHttpClient(new TransportException('reset'));
        $this->expectException(TransportException::class);
        $this->connection($http)->json(Method::Post, 'pullzone');
    }

    public function testGivesUpAfterMaxRetries(): void
    {
        $http = new MockHttpClient(new Response(503), new Response(503), new Response(503));

        $this->expectException(ServerException::class);

        $this->connection($http, new ClientOptions(maxRetries: 2), new FakeClock())->json(Method::Get, 'pullzone');
    }

    public function testRewindsStreamedBodiesBeforeRetrying(): void
    {
        $body = Stream::fromString('payload');
        $http = new MockHttpClient(new Response(503), new Response(201));
        $http->onSend = static function () use ($body): void {
            // Simulate the transport consuming the stream.
            stream_get_contents($body->resource);
        };

        $this->connection($http, clock: new FakeClock())->send(Method::Put, 'zone/file', body: $body);

        self::assertSame(2, $http->callCount());
        self::assertSame(['payload', 'payload'], $http->bodies);
    }

    public function testDoesNotRetryNonSeekableStreams(): void
    {
        $pair = stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
        self::assertIsArray($pair);
        fwrite($pair[1], 'data');
        fclose($pair[1]);
        $http = new MockHttpClient(new Response(503));

        try {
            $this->connection($http, clock: new FakeClock())->send(Method::Put, 'zone/file', body: new Stream($pair[0]));
            self::fail('Expected a ServerException.');
        } catch (ServerException) {
            self::assertSame(1, $http->callCount());
        }
    }

    public function testTruncatesTheSinkBeforeRetrying(): void
    {
        $sink = Stream::temporary();
        $http = new MockHttpClient(new TransportException('reset'), new Response(200));
        $http->onSend = static function () use ($sink): void {
            fwrite($sink->resource, 'partial');
        };

        $this->connection($http, clock: new FakeClock())->send(Method::Get, 'zone/file', sink: $sink);

        rewind($sink->resource);
        self::assertSame('partial', $sink->contents());
    }

    /**
     * @param class-string<ApiException> $expected
     */
    #[DataProvider('errorStatuses')]
    public function testMapsErrorStatusesToExceptions(int $status, string $expected): void
    {
        $http = new MockHttpClient(new Response(
            $status,
            '{"ErrorKey":"pullZone.not_found","Field":"PullZone","Message":"The requested Pull Zone was not found"}',
            ['cdn-requestid' => 'abc123'],
            'Not Found',
        ));

        try {
            $this->connection($http, new ClientOptions(maxRetries: 0))->json(Method::Get, 'pullzone/1?x=secret');
            self::fail('Expected an exception.');
        } catch (ApiException $e) {
            self::assertInstanceOf($expected, $e);
            self::assertSame($status, $e->statusCode);
            self::assertSame('pullZone.not_found', $e->errorKey);
            self::assertSame('PullZone', $e->field);
            self::assertSame('abc123', $e->requestId);
            self::assertStringContainsString('The requested Pull Zone was not found', $e->getMessage());
            self::assertStringNotContainsString('secret', $e->getMessage());
        }
    }

    /**
     * @return iterable<string, array{int, class-string<ApiException>}>
     */
    public static function errorStatuses(): iterable
    {
        yield '400' => [400, BadRequestException::class];
        yield '401' => [401, AuthenticationException::class];
        yield '403' => [403, ForbiddenException::class];
        yield '404' => [404, NotFoundException::class];
        yield '409' => [409, ConflictException::class];
        yield '422' => [422, ValidationException::class];
        yield '429' => [429, RateLimitException::class];
        yield '500' => [500, ServerException::class];
        yield '418' => [418, ApiException::class];
    }

    public function testRejectsInvalidJson(): void
    {
        $this->expectException(SerializationException::class);

        $this->connection(new MockHttpClient(new Response(200, '{broken')))->json(Method::Get, 'pullzone');
    }

    public function testRejectsInvalidBaseUri(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Connection('ftp://example.com', 'key', new ClientOptions(), new MockHttpClient(), new SpyRateLimiter());
    }

    public function testRejectsEmptyAccessKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Connection('https://api.bunny.net', ' ', new ClientOptions(), new MockHttpClient(), new SpyRateLimiter());
    }

    public function testHidesTheAccessKeyFromDumps(): void
    {
        $dump = print_r($this->connection(new MockHttpClient()), true);

        self::assertStringNotContainsString('secret-key', $dump);
    }

    public function testRaisesErrorsReportedInSuccessfulResponses(): void
    {
        $http = new MockHttpClient(new Response(202, '{"error":"missing"}'), new Response(200, '{"ok":true}'));
        $errorStatus = static fn(Response $response): ?int => str_contains($response->body, 'error') ? 404 : null;
        $connection = new Connection('https://api.bunny.net', 'secret-key', new ClientOptions(), $http, new SpyRateLimiter(), new FakeClock(), $errorStatus);

        try {
            $connection->json(Method::Get, 'thing');
            self::fail('Expected a NotFoundException.');
        } catch (NotFoundException $e) {
            // Classified as 404, but the actual status is kept.
            self::assertSame(202, $e->statusCode);
        }

        self::assertSame(['ok' => true], $connection->json(Method::Get, 'thing'));
        self::assertCount(2, $http->requests);
    }

    public function testLetsTheHookPickTheExceptionOfErrorResponses(): void
    {
        $http = new MockHttpClient(new Response(401, 'rejected'), new Response(401, 'denied'));
        $errorStatus = static fn(Response $response): ?int => $response->body === 'rejected' ? 400 : null;
        $connection = new Connection('https://api.bunny.net', 'secret-key', new ClientOptions(), $http, new SpyRateLimiter(), new FakeClock(), $errorStatus);

        try {
            $connection->json(Method::Get, 'thing');
            self::fail('Expected a BadRequestException.');
        } catch (BadRequestException $e) {
            self::assertSame(401, $e->statusCode);
        }

        $this->expectException(AuthenticationException::class);
        $connection->json(Method::Get, 'thing');
    }

    private function connection(
        MockHttpClient $http,
        ClientOptions $options = new ClientOptions(),
        FakeClock $clock = new FakeClock(),
        SpyRateLimiter $rateLimiter = new SpyRateLimiter(),
    ): Connection {
        return new Connection('https://api.bunny.net/', 'secret-key', $options, $http, $rateLimiter, $clock);
    }
}
