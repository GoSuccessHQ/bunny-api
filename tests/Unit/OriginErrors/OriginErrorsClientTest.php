<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\OriginErrors;

use DateTimeImmutable;
use DateTimeZone;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\OriginErrors\Model\OriginError;
use GoSuccess\Bunny\OriginErrors\Model\OriginErrorLog;
use GoSuccess\Bunny\OriginErrors\OriginErrorsClient;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OriginErrorsClient::class)]
#[CoversClass(OriginErrorLog::class)]
#[CoversClass(OriginError::class)]
final class OriginErrorsClientTest extends TestCase
{
    /** The example response from bunny.net's documentation, plus the undocumented fields. */
    private const string RESPONSE = <<<'JSON'
        {
          "logs": [
            {
              "logId": "a6a6b755-b6a4-46be-b523-aa82a17d4bc5",
              "timestamp": 1728952065848,
              "log": "{\"RequestUrl\":\"/apikey\",\"PullZoneId\":308006,\"Message\":\"Origin DNS lookup failed...\",\"ErrorCode\":\"dns_lookup\",\"StatusCode\":502}",
              "labels": {"ErrorCode": "dns_lookup", "StatusCode": "502", "ServerZone": "CA"}
            }
          ],
          "hasMoreData": true,
          "continuationToken": "next",
          "startToken": ""
        }
        JSON;

    public function testRequestsTheDayInUtc(): void
    {
        $http = new MockHttpClient(new Response(200, '{"logs":[]}'));

        new OriginErrorsClient('secret', httpClient: $http)->get(308006, new DateTimeImmutable('2026-09-18 01:30:00', new DateTimeZone('Europe/Berlin')));

        self::assertSame('https://cdn-origin-logging.bunny.net/308006/09-17-2026', $http->requests[0]->uri);
        self::assertSame('secret', $http->requests[0]->headers['AccessKey']);
    }

    public function testDecodesTheLogEntries(): void
    {
        $log = new OriginErrorsClient('secret', httpClient: new MockHttpClient(new Response(200, self::RESPONSE)))
            ->get(308006, new DateTimeImmutable());

        self::assertTrue($log->hasMoreData);
        self::assertSame('next', $log->continuationToken);
        self::assertNull($log->startToken);
        self::assertCount(1, $log->errors);

        $error = $log->errors[0];
        self::assertSame('a6a6b755-b6a4-46be-b523-aa82a17d4bc5', $error->id);
        self::assertSame('2024-10-15T00:27:45.848+00:00', $error->timestamp?->format('Y-m-d\TH:i:s.vP'));
        self::assertSame('dns_lookup', $error->errorCode);
        self::assertSame(502, $error->statusCode);
        self::assertSame('CA', $error->serverZone);
        self::assertSame('/apikey', $error->requestUrl);
        self::assertSame(308006, $error->pullZoneId);
        self::assertSame('Origin DNS lookup failed...', $error->message);
        self::assertSame('dns_lookup', $error->details['ErrorCode']);
    }

    public function testToleratesAMalformedLogField(): void
    {
        $error = OriginError::fromArray(['logId' => 'x', 'log' => 'not json', 'labels' => ['StatusCode' => '504']]);

        self::assertSame(504, $error->statusCode);
        self::assertSame([], $error->details);
        self::assertNull($error->timestamp);
    }
}
