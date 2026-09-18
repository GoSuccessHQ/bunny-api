<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\Shield;

use DateTimeImmutable;
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Exception\ApiException;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Shield\Model\RateLimitRule;
use GoSuccess\Bunny\Shield\Model\ShieldZone;
use GoSuccess\Bunny\Shield\ShieldClient;
use GoSuccess\Bunny\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Read-only checks of the Shield API against a real account.
 *
 * Only side-effect-free GET requests are made. `waf->recommendation()` is
 * deliberately absent: it has an AI generate a recommendation.
 */
#[CoversNothing]
final class ShieldReadOnlyTest extends IntegrationTestCase
{
    private static ?ShieldZone $zone = null;

    public function testZones(): void
    {
        $shield = self::client();
        $zone = self::zone();
        $page = $shield->zones->list(perPage: 5);

        self::assertContainsOnlyInstancesOf(ShieldZone::class, $page->items);
        self::assertCount($page->totalItems ?? 0, iterator_to_array($shield->zones->all(perPage: 5), false));
        self::assertSame($zone->shieldZoneId, $shield->zones->get($zone->shieldZoneId)->shieldZoneId);
        self::assertNotEmpty($shield->zones->pullZoneMapping());
        $shield->zones->defaults();

        if ($zone->pullZoneId !== null) {
            self::assertSame($zone->shieldZoneId, $shield->zones->getByPullZone($zone->pullZoneId)->shieldZoneId);
        }
    }

    public function testWafAndRules(): void
    {
        $shield = self::client();
        $id = self::zone()->shieldZoneId;

        self::assertNotEmpty($shield->waf->rules($id));
        self::assertNotEmpty($shield->waf->rulesByPlan());
        self::assertNotEmpty($shield->waf->profiles());
        self::assertNotEmpty($shield->waf->enums());
        self::assertNotEmpty($shield->waf->engineConfig());
        self::assertNotEmpty($shield->ddos->enums());
        $shield->waf->triggeredRules($id);
        $shield->customRules->list($id);

        $rateLimits = iterator_to_array($shield->rateLimits->all($id), false);
        self::assertContainsOnlyInstancesOf(RateLimitRule::class, $rateLimits);

        foreach ($rateLimits as $rule) {
            self::assertSame($rule->id, $shield->rateLimits->get($rule->id)->id);
            $shield->metrics->rateLimit($rule->id);
        }
    }

    public function testProtectionFeatures(): void
    {
        $shield = self::client();
        $id = self::zone()->shieldZoneId;

        $shield->accessLists->list($id);
        self::assertNotEmpty($shield->accessLists->enums($id));
        self::assertNotEmpty($shield->apiGuardian->enums($id));
        $shield->botDetection->categorization($id);
        $shield->uploadScanning->get($id);

        try {
            $shield->botDetection->get($id);
        } catch (ApiException $e) {
            // The Basic plan has no bot detection; Shield says so with 202 Accepted.
            self::assertSame(202, $e->statusCode);
            self::assertSame('invalid_plan_type.bot_detection', $e->errorKey);
        }

        try {
            $shield->apiGuardian->get($id);
        } catch (NotFoundException $e) {
            self::assertSame('api_guardian.get_api_guardian.no_configuration', $e->errorKey);
        }
    }

    public function testMetricsAndLogs(): void
    {
        $shield = self::client();
        $id = self::zone()->shieldZoneId;
        $lastMonth = new DateTimeImmutable('first day of last month');

        $shield->metrics->overview($id);
        $shield->metrics->detailed($id);

        try {
            self::assertSame((int) $lastMonth->format('n'), $shield->metrics->overages($id, (int) $lastMonth->format('Y'), (int) $lastMonth->format('n'))->month);
        } catch (NotFoundException) {
            // No billing data of the zone in that month.
        }

        try {
            $shield->metrics->rateLimits($id);
        } catch (NotFoundException $e) {
            self::assertSame('not_found.ratelimit_shieldzone', $e->errorKey);
        }

        $shield->metrics->botDetection($id);
        $shield->metrics->uploadScanning($id);
        $shield->metrics->apiGuardian($id);
        $shield->promotions->state();

        foreach ($shield->eventLogs->all($id, new DateTimeImmutable('-1 day')) as $log) {
            self::assertNotNull($log->logId);
        }
    }

    private static function client(): ShieldClient
    {
        return new Bunny(self::apiKey())->shield;
    }

    private static function zone(): ShieldZone
    {
        if (self::$zone === null) {
            $zones = self::client()->zones->list(perPage: 1)->items;

            if ($zones === []) {
                self::markTestSkipped('The account has no Shield zones.');
            }

            self::$zone = $zones[0];
        }

        return self::$zone;
    }
}
