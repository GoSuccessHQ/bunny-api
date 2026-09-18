<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\MagicContainers;

use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Pagination\Page;

/**
 * Pagination of the Magic Containers API.
 *
 * @internal
 */
final class Pagination
{
    /**
     * Cursor-based lists: `{"items", "meta": {"totalItems"}, "cursor"}`.
     *
     * The cursor is absent on the last page. `meta.totalItems` counts the items
     * from the requested page on, so it is the total of the list only on the
     * first page (verified live).
     *
     * @template T
     *
     * @param array<array-key, mixed> $data
     * @param list<T>                 $items
     * @param string|null             $cursor The cursor the page was requested with.
     *
     * @return Page<T>
     */
    public static function cursor(array $data, array $items, ?string $cursor = null): Page
    {
        $meta = \is_array($data['meta'] ?? null) ? $data['meta'] : [];
        $next = Cast::string($data['cursor'] ?? null);

        return new Page(
            items: $items,
            totalItems: $cursor === null ? Cast::int($meta['totalItems'] ?? null) : null,
            next: $items !== [] && $next !== null && $next !== '' && $next !== $cursor ? $next : null,
        );
    }
}
