<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Core;

use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Pagination\Page;

/**
 * The two pagination styles of the Core Platform API.
 *
 * @internal
 */
final class Pagination
{
    /**
     * Page-numbered lists: `{"Items", "CurrentPage", "TotalItems", "HasMoreItems"}`.
     *
     * An empty page ends the list even if more items are announced: the API
     * reports HasMoreItems wrongly for page sizes below 5, which it silently
     * raises to 5.
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
        $current = Cast::int($data['CurrentPage'] ?? null) ?? 1;
        $hasMore = $items !== [] && (Cast::bool($data['HasMoreItems'] ?? null) ?? false);

        return new Page(
            items: $items,
            totalItems: Cast::int($data['TotalItems'] ?? null),
            currentPage: $current,
            next: $hasMore ? $current + 1 : null,
        );
    }

    /**
     * Token-based lists: `{"HasMoreData", "ContinuationToken"}`. The token is an
     * empty string on the last page.
     *
     * @template T
     *
     * @param array<array-key, mixed> $data
     * @param list<T>                 $items
     *
     * @return Page<T>
     */
    public static function continuation(array $data, array $items): Page
    {
        $token = Cast::string($data['ContinuationToken'] ?? null);
        $hasMore = Cast::bool($data['HasMoreData'] ?? null) ?? false;

        return new Page(
            items: $items,
            next: $hasMore && $token !== null && $token !== '' ? $token : null,
        );
    }
}
