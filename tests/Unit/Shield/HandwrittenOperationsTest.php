<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Shield;

use DateTimeImmutable;
use DateTimeZone;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Shield\Enum\CustomPageType;
use GoSuccess\Bunny\Shield\Model\EventLogFilter;
use GoSuccess\Bunny\Shield\Model\PromotionState;
use GoSuccess\Bunny\Shield\Pagination;
use GoSuccess\Bunny\Shield\ShieldClient;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pagination::class)]
#[CoversClass(EventLogFilter::class)]
#[CoversClass(PromotionState::class)]
final class HandwrittenOperationsTest extends TestCase
{
    public function testReadsCustomPagesAsRawHtml(): void
    {
        $http = new MockHttpClient(
            new Response(200, '<h1>Blocked</h1>', ['content-type' => 'text/plain; charset=utf-8']),
            new Response(200, '"<h1>Slow down</h1>"', ['content-type' => 'application/json; charset=utf-8']),
        );
        $pages = $this->client($http)->customPages;

        self::assertSame('<h1>Blocked</h1>', $pages->get(42, CustomPageType::Block));
        self::assertSame('https://api.bunny.net/shield/shield-zone/42/custom-page/block', $http->requests[0]->uri);
        self::assertSame('text/plain', $http->requests[0]->headers['Accept']);
        self::assertSame('<h1>Slow down</h1>', $pages->get(42, CustomPageType::RateLimit));
    }

    public function testUploadsAndDeletesCustomPages(): void
    {
        $http = new MockHttpClient(new Response(200, '{"statusCode":200,"success":true}'), new Response(200, '{"statusCode":200,"success":true}'));
        $pages = $this->client($http)->customPages;

        $pages->upload(42, CustomPageType::Challenge, Stream::fromString('<h1>Hold on</h1>'));
        $pages->delete(42, CustomPageType::Challenge);

        self::assertSame(Method::Put, $http->requests[0]->method);
        self::assertSame('https://api.bunny.net/shield/shield-zone/42/custom-page/challenge', $http->requests[0]->uri);
        self::assertSame('<h1>Hold on</h1>', $http->bodies[0]);
        self::assertSame(Method::Delete, $http->requests[1]->method);
    }

    public function testPagesThroughTheEventLogsOfADay(): void
    {
        $http = new MockHttpClient(
            new Response(200, '{"logs":[{"logId":"a","timestamp":1789624652913,"log":"{}","labels":{"country":"DE"}}],"hasMoreData":true,"continuationToken":"dG9r/2=","startToken":"s"}'),
            new Response(200, '{"logs":[{"logId":"b","timestamp":1789624652914}],"hasMoreData":false,"continuationToken":"","startToken":"s"}'),
        );

        $logs = iterator_to_array($this->client($http)->eventLogs->all(42, new DateTimeImmutable('2026-09-18 01:00', new DateTimeZone('Europe/Berlin'))), false);

        self::assertSame(['a', 'b'], array_map(static fn($log) => $log->logId, $logs));
        self::assertSame('DE', $logs[0]->labels?->country);
        self::assertSame('https://api.bunny.net/shield/event-logs/42/09-17-2026', $http->requests[0]->uri);
        self::assertSame('https://api.bunny.net/shield/event-logs/42/09-17-2026/dG9r%2F2%3D', $http->requests[1]->uri);
    }

    public function testSearchesEventLogsInAWindow(): void
    {
        $http = new MockHttpClient(new Response(200, '{"rows":[],"groups":[{"key":{"ip":"203.0.113.9"},"count":12,"firstSeen":1789624652913,"lastSeen":1789624659000,"sparkline":[4,8]}],"total":1,"totalPages":1,"page":0,"errorResponse":null}'));

        $result = $this->client($http)->eventLogs->search(
            42,
            from: new DateTimeImmutable('@1789624652.913'),
            to: new DateTimeImmutable('@1789628252'),
            filters: [new EventLogFilter('country', 'in', ['DE', 'AT'])],
            groupBy: ['ip'],
            buckets: 2,
            pageSize: 10,
        );

        self::assertSame('https://api.bunny.net/shield/event-logs/42/search', $http->requests[0]->uri);
        self::assertSame([
            'from' => 1789624652913,
            'to' => 1789628252000,
            'filters' => [['field' => 'country', 'op' => 'in', 'value' => ['DE', 'AT']]],
            'groupBy' => ['ip'],
            'buckets' => 2,
            'page' => 0,
            'pageSize' => 10,
        ], $http->jsonBody());
        self::assertSame(1, $result->total);
        self::assertSame(['ip' => '203.0.113.9'], $result->groups[0]->key);
        self::assertSame([4, 8], $result->groups[0]->sparkline);
    }

    public function testExportsEventLogsAsCsv(): void
    {
        $http = new MockHttpClient(new Response(200, "time,ip\n1,203.0.113.9\n"), new Response(200, "time,ip\n"));
        $eventLogs = $this->client($http)->eventLogs;
        $from = new DateTimeImmutable('-1 hour');
        $to = new DateTimeImmutable();

        self::assertSame("time,ip\n1,203.0.113.9\n", $eventLogs->export(42, $from, $to, query: 'wp-login'));
        self::assertSame('text/csv', $http->requests[0]->headers['Accept']);
        self::assertSame('wp-login', $http->jsonBody()['query']);

        $sink = Stream::fromString('');
        self::assertSame('', $eventLogs->export(42, $from, $to, sink: $sink));
        self::assertSame($sink, $http->requests[1]->sink);
    }

    public function testReadsThePromotionState(): void
    {
        $http = new MockHttpClient(new Response(200, '{"currentPromos":[],"eligiblePromos":[{"name":"x"}],"enrolledPromos":[],"firstShieldZoneCreationDate":"2023-05-01T10:00:00"}'));

        $state = $this->client($http)->promotions->state();

        self::assertSame([['name' => 'x']], $state->eligiblePromos);
        self::assertSame('2023-05-01T10:00:00+00:00', $state->firstShieldZoneCreationDate?->format(\DATE_ATOM));
    }

    public function testEndsListsAtTheLastPage(): void
    {
        $next = static fn(array $page, int $count): int|string|null => Pagination::page(['page' => $page], array_fill(0, $count, 'x'))->next;

        self::assertSame(3, $next(['totalCount' => 17, 'totalPages' => 4, 'currentPage' => 2, 'nextPage' => 3], 5));
        self::assertNull($next(['totalCount' => 17, 'totalPages' => 4, 'currentPage' => 4, 'nextPage' => null], 2));
        // Past the end the API still reports a next page.
        self::assertNull($next(['totalCount' => 17, 'totalPages' => 4, 'currentPage' => 4, 'nextPage' => 5], 2));
        self::assertNull($next(['totalCount' => 17, 'totalPages' => 4, 'currentPage' => 5, 'nextPage' => 6], 0));
        // Empty lists come without the page object.
        self::assertSame(0, Pagination::page(['data' => [], 'page' => null], [])->totalItems);
    }

    private function client(MockHttpClient $http): ShieldClient
    {
        return new ShieldClient('key', httpClient: $http);
    }
}
