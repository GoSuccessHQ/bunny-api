<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Logging;

use DateTimeImmutable;
use DateTimeZone;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Logging\LoggingClient;
use GoSuccess\Bunny\Logging\Model\LegacyLog;
use GoSuccess\Bunny\Logging\Model\LegacyLogEntry;
use GoSuccess\Bunny\Logging\Pagination;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LegacyLog::class)]
#[CoversClass(LegacyLogEntry::class)]
#[CoversClass(Pagination::class)]
final class LoggingTest extends TestCase
{
    private const string LOG = "HIT|200|1507167062421|412|390|163.172.53.0|-|https://example.b-cdn.net/video.mp4|WA|Mozilla/5.0|322b688bd63fb63f2babe9de30a5d262|DE\n"
        . "\n"
        . "-|404|1507167062500|98|390|0.0.0.0|https://example.com/|https://example.b-cdn.net/missing|DE|-|a1b2|-|40|bytes=0-10|Bearer x\n";

    public function testLegacyStreamsTheDayInUtcAndParsesEveryLine(): void
    {
        $http = new MockHttpClient(new Response(200, self::LOG));
        $date = new DateTimeImmutable('2026-09-18 00:30:00', new DateTimeZone('Europe/Berlin'));

        $log = new LoggingClient('secret', httpClient: $http)->logs->legacy(390, $date, sort: 'asc', status: '4,5');

        self::assertSame('https://logging.bunnycdn.com/09-17-26/390.log?sort=asc&status=4%2C5', $http->requests[0]->uri);
        self::assertNotNull($http->requests[0]->sink);

        $entries = iterator_to_array($log);
        self::assertCount(2, $entries);

        $hit = $entries[0];
        self::assertSame('HIT', $hit->cacheStatus);
        self::assertSame(200, $hit->statusCode);
        self::assertSame('2017-10-05T01:31:02.421+00:00', $hit->timestamp?->format('Y-m-d\TH:i:s.vP'));
        self::assertSame(412, $hit->bytesSent);
        self::assertSame(390, $hit->pullZoneId);
        self::assertSame('163.172.53.0', $hit->remoteIp);
        self::assertNull($hit->referer);
        self::assertSame('https://example.b-cdn.net/video.mp4', $hit->url);
        self::assertSame('WA', $hit->edgeLocation);
        self::assertSame('Mozilla/5.0', $hit->userAgent);
        self::assertSame('322b688bd63fb63f2babe9de30a5d262', $hit->requestId);
        self::assertSame('DE', $hit->countryCode);
        self::assertNull($hit->bodyBytesSent);

        $miss = $entries[1];
        self::assertNull($miss->cacheStatus);
        self::assertSame('https://example.com/', $miss->referer);
        self::assertNull($miss->userAgent);
        self::assertNull($miss->countryCode);
        self::assertSame(40, $miss->bodyBytesSent);
        self::assertSame('bytes=0-10', $miss->rangeHeader);
        self::assertSame('Bearer x', $miss->authorizationHeader);

        // Iterating again starts over.
        self::assertCount(2, iterator_to_array($log));
    }

    public function testOffsetPaginationAdvancesByTheLimit(): void
    {
        $page = Pagination::offset(['pagination' => ['offset' => 200, 'limit' => 100, 'returned' => 3, 'hasMore' => true]], ['a', 'b', 'c']);
        self::assertSame(300, $page->next);

        // Rows are filtered after fetching, so an empty page may still announce more.
        self::assertSame(100, Pagination::offset(['pagination' => ['offset' => 0, 'limit' => 100, 'returned' => 0, 'hasMore' => true]], [])->next);

        self::assertNull(Pagination::offset(['pagination' => ['offset' => 0, 'limit' => 100, 'hasMore' => false]], ['a'])->next);
    }
}
