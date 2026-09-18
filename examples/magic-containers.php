<?php

declare(strict_types=1);

/**
 * List the Magic Containers applications with their status and the account's
 * limits, the regions and the number of node IP addresses.
 */

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\Region;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

$containers = $bunny->magicContainers;
$limits = $containers->limits->get();

echo "Applications: {$limits->existingNumberOfApplications} of {$limits->maxNumberOfApplications}\n";

foreach ($containers->apps->all() as $app) {
    echo "  {$app->name} ({$app->id}): {$app->status?->value}\n";
}

$regions = array_map(static fn(Region $region): string => $region->id, iterator_to_array($containers->regions->all(), false));
$optimal = $containers->regions->optimal();

echo 'Regions: ', implode(', ', $regions), "\n";
echo "Closest region: {$optimal->name} ({$optimal->id})\n";
echo 'Node IP addresses: ', count($containers->nodes->plain()), "\n";
