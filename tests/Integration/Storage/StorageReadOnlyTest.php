<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\Storage;

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Storage\Model\StorageObject;
use GoSuccess\Bunny\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Read-only checks of the Edge Storage API against a real account.
 *
 * Uses the zone's read-only password, which cannot modify anything.
 */
#[CoversNothing]
final class StorageReadOnlyTest extends IntegrationTestCase
{
    public function testListsDescribesAndDownloadsWithTheReadOnlyPassword(): void
    {
        $bunny = new Bunny(self::apiKey());
        $zones = $bunny->core->storageZones->list(perPage: 10)->items;

        if ($zones === []) {
            self::markTestSkipped('The account has no storage zones.');
        }

        $storage = $bunny->storageFor($bunny->core->storageZones->get($zones[0]->id), readOnly: true);
        $objects = $storage->list();
        self::assertContainsOnlyInstancesOf(StorageObject::class, $objects);
        self::assertFalse($storage->exists('does-not-exist-' . bin2hex(random_bytes(4)) . '.txt'));

        foreach ($objects as $object) {
            if ($object->isDirectory) {
                continue;
            }

            $described = $storage->describe($object->relativePath);
            self::assertSame($object->guid, $described->guid);

            $contents = $storage->get($object->relativePath);
            self::assertSame($object->length, \strlen($contents));

            if ($object->checksum !== null) {
                self::assertSame($object->checksum, strtoupper(hash('sha256', $contents)));
            }

            return;
        }

        self::markTestSkipped('The storage zone root holds no files.');
    }
}
