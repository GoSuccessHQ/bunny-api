<?php

declare(strict_types=1);

/**
 * Typed exceptions for API errors.
 */

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Exception\ApiException;
use GoSuccess\Bunny\Exception\BunnyException;
use GoSuccess\Bunny\Exception\NotFoundException;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

try {
    $bunny->core->pullZones->get(1);
} catch (NotFoundException $e) {
    // HTTP 404, with the details bunny.net reported.
    printf("Not found: %s\n", $e->getMessage());
    printf("Error key: %s\n", $e->errorKey ?? '-');
    printf("Request id for bunny.net support: %s\n", $e->requestId ?? '-');
} catch (ApiException $e) {
    // Any other HTTP error status.
    printf("HTTP %d: %s\n", $e->statusCode, $e->getMessage());
} catch (BunnyException $e) {
    // Network failures, unexpected responses, ...
    printf("%s: %s\n", $e::class, $e->getMessage());
}
