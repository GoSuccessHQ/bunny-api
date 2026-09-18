<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Pagination;

use Closure;
use Generator;
use IteratorAggregate;

/**
 * Lazily iterates over every item of a list endpoint, across all pages.
 *
 * Pages are only requested while the iteration proceeds, so breaking out of a
 * loop early saves the remaining requests. Keys are sequential, which makes the
 * paginator safe to pass to `iterator_to_array()`.
 *
 * @template-covariant T
 *
 * @implements IteratorAggregate<int, T>
 */
final readonly class Paginator implements IteratorAggregate
{
    /**
     * @param Closure(int|string|null): Page<T> $fetchPage Fetches the page at the given
     *                                                     position; null requests the first one.
     */
    public function __construct(private Closure $fetchPage) {}

    /**
     * @return Generator<int, T>
     */
    public function getIterator(): Generator
    {
        $position = null;
        $index = 0;

        do {
            $page = ($this->fetchPage)($position);

            foreach ($page->items as $item) {
                yield $index++ => $item;
            }

            // Stop on an empty page or a position that does not advance, so a
            // misbehaving endpoint cannot cause an endless loop.
            $previous = $position;
            $position = $page->items === [] || $page->next === $previous ? null : $page->next;
        } while ($position !== null);
    }
}
