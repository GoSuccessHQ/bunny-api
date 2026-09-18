<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Http;

use GoSuccess\Bunny\Exception\TransportException;
use GoSuccess\Bunny\Http\CurlHttpClient;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Request;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Tests\Support\LocalServer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the real cURL transport against a local PHP web server.
 */
#[CoversClass(CurlHttpClient::class)]
final class CurlHttpClientTest extends TestCase
{
    private static ?LocalServer $server = null;

    public static function setUpBeforeClass(): void
    {
        self::$server = new LocalServer();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server?->stop();
        self::$server = null;
    }

    public function testGetReturnsStatusBodyAndLowerCasedHeaders(): void
    {
        $response = $this->send(new Request(Method::Get, $this->uri('/echo?a=1'), ['X-Test' => 'yes']));

        self::assertSame(200, $response->statusCode);
        self::assertTrue($response->isSuccessful);
        self::assertSame('one', $response->header('X-CUSTOM'));
        self::assertSame('OK', $response->reasonPhrase);

        $echo = $this->decode($response);
        self::assertSame('GET', $echo['method']);
        self::assertSame('a=1', $echo['query']);
        self::assertIsArray($echo['headers']);
        self::assertSame('yes', $echo['headers']['x-test']);
    }

    public function testBodylessPostSendsZeroContentLengthWithoutFormContentType(): void
    {
        $echo = $this->decode($this->send(new Request(Method::Post, $this->uri('/echo'))));

        self::assertIsArray($echo['headers']);
        self::assertSame('0', $echo['headers']['content-length']);
        self::assertArrayNotHasKey('content-type', $echo['headers']);
    }

    public function testStringBodyIsSentWithTheGivenContentType(): void
    {
        $echo = $this->decode($this->send(new Request(
            Method::Post,
            $this->uri('/echo'),
            ['Content-Type' => 'application/json'],
            '{"Name":"zone"}',
        )));

        self::assertSame('{"Name":"zone"}', $echo['body']);
        self::assertIsArray($echo['headers']);
        self::assertSame('application/json', $echo['headers']['content-type']);
    }

    public function testStreamBodyIsUploadedWithItsLength(): void
    {
        $data = random_bytes(300_000);
        $echo = $this->decode($this->send(new Request(
            Method::Put,
            $this->uri('/echo'),
            ['Content-Type' => 'application/octet-stream'],
            Stream::fromString($data),
        )));

        self::assertSame('PUT', $echo['method']);
        self::assertSame(\strlen($data), $echo['bodyLength']);
        self::assertSame(hash('sha256', $data), $echo['bodySha256']);
        self::assertIsArray($echo['headers']);
        self::assertSame((string) \strlen($data), $echo['headers']['content-length']);
    }

    public function testStreamBodySendsNoMoreThanItsSize(): void
    {
        $stream = Stream::fromString('abcdefghij');
        fseek($stream->resource, 2);

        $echo = $this->decode($this->send(new Request(
            Method::Patch,
            $this->uri('/echo'),
            ['Content-Type' => 'application/offset+octet-stream'],
            new Stream($stream->resource, 5),
        )));

        self::assertSame('PATCH', $echo['method']);
        self::assertSame('cdefg', $echo['body']);
        self::assertSame(7, ftell($stream->resource));
    }

    public function testHeadReturnsTheHeadersWithoutWaitingForABody(): void
    {
        $response = $this->send(new Request(Method::Head, $this->uri('/echo'), timeout: 5.0));

        self::assertSame(200, $response->statusCode);
        self::assertSame('one', $response->header('x-custom'));
        self::assertSame('', $response->body);
    }

    public function testSuccessfulResponseIsWrittenToTheSink(): void
    {
        $sink = Stream::temporary();
        $response = $this->send(new Request(Method::Get, $this->uri('/download/200000'), sink: $sink));

        self::assertSame(200, $response->statusCode);
        self::assertSame('', $response->body);
        rewind($sink->resource);
        self::assertSame(200_000, \strlen($sink->contents()));
    }

    public function testErrorResponseIsKeptOutOfTheSink(): void
    {
        $sink = Stream::temporary();
        $response = $this->send(new Request(Method::Get, $this->uri('/status/404'), sink: $sink));

        self::assertSame(404, $response->statusCode);
        self::assertStringContainsString('Status 404', $response->body);
        rewind($sink->resource);
        self::assertSame('', $sink->contents());
    }

    public function testConnectionFailureThrowsTransportException(): void
    {
        $port = LocalServer::closedPort();

        $this->expectException(TransportException::class);

        new CurlHttpClient(connectTimeout: 1.0)->send(new Request(Method::Get, "http://127.0.0.1:{$port}/"));
    }

    public function testRequestTimeoutOverridesTheDefault(): void
    {
        $this->expectException(TransportException::class);

        $this->send(new Request(Method::Get, $this->uri('/sleep/2000'), timeout: 0.3));
    }

    public function testTransportErrorMessageOmitsTheQueryString(): void
    {
        $port = LocalServer::closedPort();

        try {
            new CurlHttpClient(connectTimeout: 1.0)->send(new Request(Method::Get, "http://127.0.0.1:{$port}/play?token=secret"));
            self::fail('Expected a TransportException.');
        } catch (TransportException $e) {
            self::assertStringNotContainsString('secret', $e->getMessage());
        }
    }

    /**
     * @return non-empty-string
     */
    private function uri(string $path): string
    {
        self::assertNotNull(self::$server);

        return self::$server->baseUri . $path;
    }

    private function send(Request $request): Response
    {
        return new CurlHttpClient()->send($request);
    }

    /**
     * @return array<array-key, mixed>
     */
    private function decode(Response $response): array
    {
        $decoded = json_decode($response->body, true);
        self::assertIsArray($decoded);

        return $decoded;
    }
}
