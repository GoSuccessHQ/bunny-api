<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream;

use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Pagination\Page;

/**
 * Pagination of the Stream API.
 *
 * @internal
 */
final class Pagination
{
    /**
     * Page-numbered lists: `{"items", "currentPage", "itemsPerPage", "totalItems"}`.
     *
     * The next page is derived from the effective page size the API reports,
     * because it clamps the requested one to 10…1000.
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
        $current = Cast::int($data['currentPage'] ?? null) ?? 1;
        $perPage = Cast::int($data['itemsPerPage'] ?? null) ?? \count($items);
        $total = Cast::int($data['totalItems'] ?? null);
        $hasMore = $items !== [] && $total !== null && $perPage > 0 && $current * $perPage < $total;

        return new Page(
            items: $items,
            totalItems: $total,
            currentPage: $current,
            next: $hasMore ? $current + 1 : null,
        );
    }
}
