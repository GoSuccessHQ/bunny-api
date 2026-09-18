<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Config;

use RuntimeException;

/**
 * A pagination style of an API.
 *
 * The generated list method reads the items itself (typed) and hands the
 * decoded payload plus the items to a hand-written factory, which knows how
 * the API reports the position of the next page.
 */
final readonly class PaginationConfig
{
    /**
     * @param string          $name     Style name referenced by methods.
     * @param string          $position Query parameter carrying the position (page, offset, cursor).
     * @param 'int'|'string'  $positionType
     * @param int|null        $first    Position of the first page for int positions (1 for pages, 0 for offsets).
     * @param string|null     $size     Query parameter carrying the page size.
     * @param int|null        $pageSize Default page size of the list method.
     * @param int|null        $allSize  Page size used when iterating over all items.
     * @param string          $items    Response property holding the items.
     * @param string          $factory  "Class::method", see ApiConfig::referencedClass(), called
     *                                  as factory(array $data, list $items): Page.
     */
    public function __construct(
        public string $name,
        public string $position,
        public string $positionType,
        public ?int $first,
        public ?string $size,
        public ?int $pageSize,
        public ?int $allSize,
        public string $items,
        public string $factory,
    ) {}

    public static function fromArray(string $name, ConfigReader $reader): self
    {
        $positionType = $reader->string('positionType');

        if ($positionType !== 'int' && $positionType !== 'string') {
            throw new RuntimeException("pagination.{$name}: positionType must be int or string.");
        }

        $instance = new self(
            name: $name,
            position: $reader->string('position'),
            positionType: $positionType,
            first: $reader->optionalInt('first'),
            size: $reader->optionalString('size'),
            pageSize: $reader->optionalInt('pageSize'),
            allSize: $reader->optionalInt('allSize'),
            items: $reader->string('items'),
            factory: $reader->string('factory'),
        );

        $reader->assertNoUnknownKeys();

        return $instance;
    }
}
