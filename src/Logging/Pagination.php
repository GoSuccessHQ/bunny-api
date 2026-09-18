<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Logging;

use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Pagination\Page;

/**
 * Pagination of the CDN Logging API.
 *
 * @internal
 */
final class Pagination
{
    /**
     * Offset-based lists: `{"pagination": {"offset", "limit", "returned", "hasMore"}}`.
     *
     * The next offset is the current one plus the limit, as bunny.net documents
     * it: rows are filtered by country and search text after a page was
     * fetched, so a page may hold fewer rows than the limit, or none, while
     * more follow.
     *
     * @template T
     *
     * @param array<array-key, mixed> $data
     * @param list<T>                 $items
     *
     * @return Page<T>
     */
    public static function offset(array $data, array $items): Page
    {
        $pagination = Cast::object($data['pagination'] ?? null) ?? [];
        $offset = Cast::int($pagination['offset'] ?? null) ?? 0;
        $limit = Cast::int($pagination['limit'] ?? null) ?? 0;
        $hasMore = Cast::bool($pagination['hasMore'] ?? null) ?? false;

        return new Page(
            items: $items,
            next: $hasMore && $limit > 0 ? $offset + $limit : null,
        );
    }
}
