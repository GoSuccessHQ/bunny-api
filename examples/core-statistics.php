<?php

declare(strict_types=1);

/**
 * Traffic statistics of the last seven days, per day.
 */

use GoSuccess\Bunny\Bunny;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

$statistics = $bunny->core->statistics->get(
    dateFrom: new DateTimeImmutable('-7 days'),
    dateTo: new DateTimeImmutable(),
);

printf("Bandwidth: %.2f GB, requests: %d, cache hit rate: %.1f %%\n\n", $statistics->totalBandwidthUsed / 1e9, $statistics->totalRequestsServed, $statistics->cacheHitRate);

// Chart data is keyed by date.
foreach ($statistics->bandwidthUsedChart as $date => $bytes) {
    printf("%s  %8.2f GB\n", substr((string) $date, 0, 10), $bytes / 1e9);
}
