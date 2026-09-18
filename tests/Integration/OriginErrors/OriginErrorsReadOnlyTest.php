<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\OriginErrors;

use DateTimeImmutable;
use GoSuccess\Bunny\Core\CoreClient;
use GoSuccess\Bunny\OriginErrors\Model\OriginError;
use GoSuccess\Bunny\OriginErrors\OriginErrorsClient;
use GoSuccess\Bunny\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Read-only check of the Origin Errors API against a real account.
 */
#[CoversNothing]
final class OriginErrorsReadOnlyTest extends IntegrationTestCase
{
    public function testReadsTheErrorsOfARecentDay(): void
    {
        $pullZones = new CoreClient(self::apiKey())->pullZones->list(perPage: 5)->items;

        if ($pullZones === []) {
            self::markTestSkipped('The account has no pull zones.');
        }

        $log = new OriginErrorsClient(self::apiKey())->get($pullZones[0]->id, new DateTimeImmutable('yesterday'));

        self::assertContainsOnlyInstancesOf(OriginError::class, $log->errors);
    }
}
