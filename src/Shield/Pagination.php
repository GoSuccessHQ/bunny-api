<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Shield;

use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Pagination\Page;

/**
 * Pagination of the Shield API.
 *
 * @internal
 */
final class Pagination
{
    /**
     * Page-numbered lists: `{"data": [...], "page": {"totalCount", "totalPages",
     * "currentPage", "nextPage", "pageSize"}}`.
     *
     * An empty list comes without the page object. Past the last page the API
     * still reports a next page, so a list also ends at an empty page and at
     * the reported number of pages (verified live).
     *
     * @template T
     *
     * @param array<array-key, mixed> $data
     * @param list<T>                 $items
     *
     * @return Page<T>
     */
    public static function page(array $data, array $items): Page
    {
        $page = \is_array($data['page'] ?? null) ? $data['page'] : [];
        $current = Cast::int($page['currentPage'] ?? null) ?? 1;
        $pages = Cast::int($page['totalPages'] ?? null);
        $next = Cast::int($page['nextPage'] ?? null);
        $hasMore = $items !== [] && $next !== null && $next > $current && ($pages === null || $current < $pages);

        return new Page(
            items: $items,
            totalItems: Cast::int($page['totalCount'] ?? null) ?? ($items === [] ? 0 : null),
            currentPage: $current,
            next: $hasMore ? $next : null,
        );
    }
}
