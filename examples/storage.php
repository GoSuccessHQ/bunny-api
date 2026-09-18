<?php

declare(strict_types=1);

/**
 * Browse the first storage zone with its read-only password.
 */

use GoSuccess\Bunny\Bunny;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

$zones = $bunny->core->storageZones->list()->items;

if ($zones === []) {
    exit("The account has no storage zones.\n");
}

// get() returns the zone including its passwords; the read-only one cannot modify anything.
$storage = $bunny->storageFor($bunny->core->storageZones->get($zones[0]->id), readOnly: true);

printf("Storage zone %s:\n", $storage->zone);

foreach ($storage->list() as $object) {
    printf(
        "  %-40s %10s  %s  replicated to %s\n",
        $object->relativePath,
        $object->isDirectory ? 'dir' : number_format($object->length) . ' B',
        $object->lastChanged?->format('Y-m-d H:i') ?? '',
        $object->replicatedZones === [] ? '-' : implode(', ', $object->replicatedZones),
    );
}
