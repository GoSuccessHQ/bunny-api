<?php

declare(strict_types=1);

/**
 * List the edge scripts with their type, hostname, linked pull zones and
 * active release.
 */

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\EdgeScripting\Model\LinkedPullZone;
use GoSuccess\Bunny\Exception\NotFoundException;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

$scripting = $bunny->edgeScripting;
$count = 0;

foreach ($scripting->scripts->all(includeLinkedPullZones: true) as $script) {
    ++$count;
    $pullZones = implode(', ', array_map(static fn(LinkedPullZone $pullZone): string => $pullZone->pullZoneName ?? (string) $pullZone->id, $script->linkedPullZones));

    echo "{$script->name} ({$script->scriptType?->name}): {$script->defaultHostname}\n";
    echo '  Linked pull zones: ', $pullZones === '' ? '-' : $pullZones, "\n";

    try {
        $release = $scripting->releases->active($script->id);
        echo "  Active release: {$release->uuid}, published {$release->datePublished?->format('Y-m-d H:i')}\n";
    } catch (NotFoundException) {
        echo "  Not published yet\n";
    }
}

echo "{$count} edge script(s)\n";
