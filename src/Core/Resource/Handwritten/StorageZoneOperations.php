<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Core\Resource\Handwritten;

use GoSuccess\Bunny\Core\Resource\StorageZoneResource;
use GoSuccess\Bunny\Http\Method;

/**
 * Hand-written methods of {@see StorageZoneResource}.
 */
trait StorageZoneOperations
{
    /**
     * Check whether a storage zone name is still available.
     *
     * `POST /storagezone/checkavailability`
     *
     * @param string $name The storage zone name to check.
     */
    public function checkAvailability(string $name): bool
    {
        return Availability::read($this->connection->json(Method::Post, 'storagezone/checkavailability', body: ['Name' => $name]));
    }
}
