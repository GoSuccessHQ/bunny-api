<?php

declare(strict_types=1);

/**
 * List the Shield zones with their plan, WAF mode and rate limits, and
 * yesterday's event logs of the first zone.
 */

use GoSuccess\Bunny\Bunny;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

$shield = $bunny->shield;
$first = null;

foreach ($shield->zones->all() as $zone) {
    $first ??= $zone;
    $rateLimits = iterator_to_array($shield->rateLimits->all($zone->shieldZoneId), false);

    printf(
        "Shield zone %d (pull zone %s): %s plan, WAF %s, %d rate limit(s)\n",
        $zone->shieldZoneId,
        $zone->pullZoneId ?? '-',
        $zone->planType->name ?? 'unknown',
        $zone->wafEnabled === true ? $zone->wafExecutionMode->name ?? 'on' : 'off',
        count($rateLimits),
    );

    foreach ($rateLimits as $rule) {
        $configuration = $rule->ruleConfiguration;

        echo "  {$rule->ruleName}: {$configuration?->requestCount} requests {$configuration?->timeframe?->name}\n";
    }
}

if ($first === null) {
    exit("The account has no Shield zones.\n");
}

$count = 0;

foreach ($shield->eventLogs->all($first->shieldZoneId, new DateTimeImmutable('yesterday')) as $log) {
    ++$count;
}

echo "Event logs of zone {$first->shieldZoneId} yesterday: {$count}\n";
