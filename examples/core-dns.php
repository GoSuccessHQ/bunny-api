<?php

declare(strict_types=1);

/**
 * List the DNS zones of the account and the records of the first zone.
 */

use GoSuccess\Bunny\Bunny;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

$zones = $bunny->core->dnsZones->list(page: 1, perPage: 100);
printf("%d DNS zones\n", $zones->totalItems ?? count($zones->items));

foreach ($zones->items as $zone) {
    printf("  #%d %s\n", $zone->id, $zone->domain ?? '?');
}

if ($zones->items === []) {
    exit(0);
}

$first = $zones->items[0];
printf("\nRecords of %s:\n", $first->domain ?? '?');

foreach ($bunny->core->dnsRecords->all($first->id) as $record) {
    printf("  %-6s %-30s %s (TTL %d)\n", $record->type->name ?? '?', $record->name === '' || $record->name === null ? '@' : $record->name, $record->value ?? '', $record->ttl);
}

// The whole zone as a BIND zone file:
echo "\n", $bunny->core->dnsZones->export($first->id);
