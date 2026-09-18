<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Pagination;

use GoSuccess\Bunny\Pagination\Page;
use GoSuccess\Bunny\Pagination\Paginator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Paginator::class)]
#[CoversClass(Page::class)]
final class PaginatorTest extends TestCase
{
    public function testIteratesAcrossAllPagesWithSequentialKeys(): void
    {
        $requested = [];
        $pages = [
            '' => new Page(['a', 'b'], totalItems: 5, currentPage: 1, next: 2),
            2 => new Page(['c', 'd'], totalItems: 5, currentPage: 2, next: 3),
            3 => new Page(['e'], totalItems: 5, currentPage: 3),
        ];

        $paginator = new Paginator(static function (int|string|null $position) use ($pages, &$requested): Page {
            $requested[] = $position;

            return $pages[$position ?? ''];
        });

        self::assertSame(['a', 'b', 'c', 'd', 'e'], iterator_to_array($paginator));
        self::assertSame([null, 2, 3], $requested);
    }

    public function testFetchesLazily(): void
    {
        $calls = 0;
        $paginator = new Paginator(static function (int|string|null $position) use (&$calls): Page {
            ++$calls;

            return new Page(['x'], next: 'cursor-' . $calls);
        });

        foreach ($paginator as $item) {
            break;
        }

        self::assertSame(1, $calls);
    }

    public function testStopsOnAnEmptyPageEvenIfMoreAreAnnounced(): void
    {
        $paginator = new Paginator(static fn(int|string|null $position): Page => new Page([], next: 2));

        self::assertSame([], iterator_to_array($paginator));
    }

    public function testStopsWhenThePositionDoesNotAdvance(): void
    {
        $calls = 0;
        $paginator = new Paginator(static function (int|string|null $position) use (&$calls): Page {
            ++$calls;

            return new Page(['x'], next: 'same');
        });

        self::assertCount(2, iterator_to_array($paginator));
        self::assertSame(2, $calls);
    }

    public function testPageReportsWhetherMoreFollow(): void
    {
        self::assertTrue(new Page(['a'], next: 2)->hasMore);
        self::assertFalse(new Page(['a'])->hasMore);
    }
}
