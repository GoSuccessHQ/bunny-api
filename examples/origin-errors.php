<?php

declare(strict_types=1);

/**
 * The origin errors of every pull zone from yesterday.
 */

use GoSuccess\Bunny\Bunny;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

$yesterday = new DateTimeImmutable('yesterday');

foreach ($bunny->core->pullZones->all() as $zone) {
    $log = $bunny->originErrors->get($zone->id, $yesterday);

    if ($log->errors === []) {
        continue;
    }

    printf("%s: %d origin errors%s\n", $zone->name ?? $zone->id, count($log->errors), $log->hasMoreData ? ' (more exist)' : '');

    foreach ($log->errors as $error) {
        printf(
            "  %s  %d %-24s %s  %s\n",
            $error->timestamp?->format('H:i:s') ?? '--:--:--',
            $error->statusCode ?? 0,
            $error->errorCode ?? '?',
            $error->serverZone ?? '--',
            $error->requestUrl ?? '',
        );
    }
}
