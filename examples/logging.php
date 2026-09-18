<?php

declare(strict_types=1);

/**
 * The most recent 4xx and 5xx requests of the first pull zone with logging enabled.
 */

use GoSuccess\Bunny\Bunny;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

foreach ($bunny->core->pullZones->all() as $zone) {
    if (!$zone->enableLogging) {
        continue;
    }

    printf("Errors of %s in the last 24 hours:\n", $zone->name ?? $zone->id);

    $count = 0;

    // all() pages through the results; stop whenever you have seen enough.
    foreach ($bunny->logging->logs->all($zone->id, from: new DateTimeImmutable('-24 hours'), status: '4xx,5xx') as $entry) {
        printf("  %s %d %s %s\n", $entry->timestamp?->format('H:i:s') ?? '--:--:--', $entry->statusCode, $entry->cacheStatus ?? '-', $entry->url ?? '');

        if (++$count === 20) {
            break;
        }
    }

    exit(0);
}

echo "No pull zone has logging enabled.\n";
