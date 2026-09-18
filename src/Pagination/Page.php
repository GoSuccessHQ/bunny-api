<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Pagination;

/**
 * One page of a list endpoint.
 *
 * The bunny.net APIs paginate in different ways (page numbers, offsets,
 * cursors); a page normalizes them into the items plus the position of the
 * next page.
 *
 * @template-covariant T
 */
final class Page
{
    /**
     * Whether another page follows this one.
     */
    public bool $hasMore {
        get => $this->next !== null;
    }

    /**
     * @param list<T>         $items       The items on this page.
     * @param int|null        $totalItems  The number of items across all pages, if reported.
     * @param int|null        $currentPage The 1-based number of this page, for page-based endpoints.
     * @param int|string|null $next        The position of the next page (page number, offset
     *                                     or cursor), or null if this is the last page.
     */
    public function __construct(
        public readonly array $items,
        public readonly ?int $totalItems = null,
        public readonly ?int $currentPage = null,
        public readonly int|string|null $next = null,
    ) {}
}
