<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Core\Resource\Handwritten;

use GoSuccess\Bunny\Core\Resource\PullZoneResource;
use GoSuccess\Bunny\Http\Method;

/**
 * Hand-written methods of {@see PullZoneResource}.
 */
trait PullZoneOperations
{
    /**
     * Check whether a pull zone name is still available.
     *
     * `POST /pullzone/checkavailability`
     *
     * @param string $name The pull zone name to check.
     */
    public function checkAvailability(string $name): bool
    {
        return Availability::read($this->connection->json(Method::Post, 'pullzone/checkavailability', body: ['Name' => $name]));
    }
}
