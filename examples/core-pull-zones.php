<?php

declare(strict_types=1);

/**
 * List all pull zones with their hostnames and edge rules.
 */

use GoSuccess\Bunny\Bunny;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

printf("%d pull zones\n\n", $bunny->core->pullZones->count());

// all() fetches the pages lazily while the loop runs.
foreach ($bunny->core->pullZones->all() as $zone) {
    printf("#%d %s → %s\n", $zone->id, $zone->name ?? '?', $zone->originUrl ?? '(no origin URL)');

    foreach ($zone->hostnames as $hostname) {
        printf("    %s%s\n", $hostname->value ?? '?', $hostname->forceSSL ? ' (HTTPS only)' : '');
    }

    foreach ($zone->edgeRules as $rule) {
        printf("    edge rule: %s [%s]%s\n", $rule->description ?? $rule->guid ?? '?', $rule->actionType->name ?? 'unknown', $rule->enabled ? '' : ' (disabled)');
    }
}
