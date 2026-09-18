<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Storage;

use InvalidArgumentException;

/**
 * The primary regions of Edge Storage zones, each with its own API host.
 *
 * The backing values are the codes the Core API uses for a storage zone's
 * `Region` (compared case-insensitively).
 */
enum StorageRegion: string
{
    case Falkenstein = 'de';
    case London = 'uk';
    case Stockholm = 'se';
    case NewYork = 'ny';
    case LosAngeles = 'la';
    case Singapore = 'sg';
    case Sydney = 'syd';
    case SaoPaulo = 'br';
    case Johannesburg = 'jh';

    /**
     * The API host of the region, e.g. `ny.storage.bunnycdn.com`.
     */
    public function host(): string
    {
        return $this === self::Falkenstein ? 'storage.bunnycdn.com' : "{$this->value}.storage.bunnycdn.com";
    }

    /**
     * The region of a Core API region code such as `DE` or `NY`.
     */
    public static function fromCode(string $code): self
    {
        return self::tryFrom(strtolower(trim($code)))
            ?? throw new InvalidArgumentException("Unknown storage region \"{$code}\".");
    }
}
