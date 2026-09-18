<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Core\Resource\Handwritten;

use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Model\Cast;

/**
 * Reads the answer of the three `checkavailability` endpoints.
 *
 * bunny.net documents them as returning "the model determining if the zone is
 * available or not" without describing it. The answer is read from its
 * `Available` field; any other shape fails loudly instead of being guessed.
 *
 * @internal
 */
final class Availability
{
    public static function read(mixed $data): bool
    {
        $available = \is_array($data) ? Cast::bool($data['Available'] ?? null) : null;

        if ($available === null) {
            throw new SerializationException('Unexpected response of the availability check: no boolean "Available" field.');
        }

        return $available;
    }
}
