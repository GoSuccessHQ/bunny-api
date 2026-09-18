<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\OriginErrors\Model;

use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Model\ResponseModel;

/**
 * The origin errors of one pull zone on one day.
 */
final readonly class OriginErrorLog implements ResponseModel
{
    /**
     * @param list<OriginError> $errors            The errors, as far as returned.
     * @param bool              $hasMoreData       Whether bunny.net holds more errors than returned.
     * @param string|null       $continuationToken Position of the remaining errors. The API does
     *                                             not document how to request them.
     * @param string|null       $startToken        Position of the first returned error.
     */
    public function __construct(
        public array $errors = [],
        public bool $hasMoreData = false,
        public ?string $continuationToken = null,
        public ?string $startToken = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            errors: Cast::modelList(OriginError::class, $data['logs'] ?? null),
            hasMoreData: Cast::bool($data['hasMoreData'] ?? null) ?? false,
            continuationToken: self::token($data['continuationToken'] ?? null),
            startToken: self::token($data['startToken'] ?? null),
        );
    }

    /**
     * The API sends an empty string for "no token".
     */
    private static function token(mixed $value): ?string
    {
        $token = Cast::string($value);

        return $token === '' ? null : $token;
    }
}
