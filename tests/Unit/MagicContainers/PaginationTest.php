<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\MagicContainers;

use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\MagicContainers\MagicContainersClient;
use GoSuccess\Bunny\MagicContainers\Pagination;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pagination::class)]
final class PaginationTest extends TestCase
{
    public function testFollowsTheCursorAndReportsTheTotalOnTheFirstPage(): void
    {
        $http = new MockHttpClient(
            new Response(200, '{"items":[{"id":"AMS"},{"id":"ASB"}],"meta":{"totalItems":3},"cursor":"c/1="}'),
            new Response(200, '{"items":[{"id":"AT"}],"meta":{"totalItems":1}}'),
        );
        $regions = new MagicContainersClient('key', httpClient: $http)->regions;

        $first = $regions->list(limit: 2);
        self::assertSame(3, $first->totalItems);
        self::assertSame('c/1=', $first->next);

        // Later pages count only the remaining items, so they report no total.
        $last = $regions->list(nextCursor: 'c/1=', limit: 2);
        self::assertNull($last->totalItems);
        self::assertNull($last->next);
        self::assertSame('https://api.bunny.net/mc/regions?nextCursor=c%2F1%3D&limit=2', $http->requests[1]->uri);
    }

    public function testStopsAtAnEmptyOrRepeatedPage(): void
    {
        self::assertNull(Pagination::cursor(['items' => [], 'cursor' => 'x'], [])->next);
        self::assertNull(Pagination::cursor(['items' => ['a'], 'cursor' => 'x'], ['a'], 'x')->next);
        self::assertNull(Pagination::cursor(['items' => ['a'], 'cursor' => ''], ['a'])->next);
    }
}
