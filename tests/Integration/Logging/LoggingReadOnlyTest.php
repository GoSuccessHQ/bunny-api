<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\Logging;

use DateTimeImmutable;
use GoSuccess\Bunny\Core\CoreClient;
use GoSuccess\Bunny\Logging\LoggingClient;
use GoSuccess\Bunny\Logging\Model\LegacyLogEntry;
use GoSuccess\Bunny\Logging\Model\LogEntry;
use GoSuccess\Bunny\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Read-only checks of the CDN Logging API against a real account.
 */
#[CoversNothing]
final class LoggingReadOnlyTest extends IntegrationTestCase
{
    public function testReadsRecentLogsThroughBothVersions(): void
    {
        $pullZoneId = $this->pullZoneWithLogging();
        $logging = new LoggingClient(self::apiKey());

        $page = $logging->logs->list($pullZoneId, from: new DateTimeImmutable('-1 day'), limit: 5);
        self::assertContainsOnlyInstancesOf(LogEntry::class, $page->items);
        self::assertLessThanOrEqual(5, \count($page->items));

        foreach ($page->items as $entry) {
            self::assertSame($pullZoneId, $entry->pullZoneId);
        }

        $legacy = iterator_to_array($logging->logs->legacy($pullZoneId, new DateTimeImmutable('yesterday'), start: 0, end: 5));
        self::assertContainsOnlyInstancesOf(LegacyLogEntry::class, $legacy);

        foreach ($legacy as $entry) {
            self::assertSame($pullZoneId, $entry->pullZoneId);
            self::assertNotNull($entry->timestamp);
        }
    }

    private function pullZoneWithLogging(): int
    {
        foreach (new CoreClient(self::apiKey())->pullZones->all() as $zone) {
            if ($zone->enableLogging) {
                return $zone->id;
            }
        }

        self::markTestSkipped('No pull zone has logging enabled.');
    }
}
