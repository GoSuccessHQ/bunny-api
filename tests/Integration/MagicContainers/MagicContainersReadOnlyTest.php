<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\MagicContainers;

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Exception\BadRequestException;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\MagicContainers\MagicContainersClient;
use GoSuccess\Bunny\MagicContainers\Model\ApplicationListItem;
use GoSuccess\Bunny\MagicContainers\Model\ContainerImageTag;
use GoSuccess\Bunny\MagicContainers\Model\ContainerRegistry;
use GoSuccess\Bunny\MagicContainers\Model\Region;
use GoSuccess\Bunny\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Read-only checks of the Magic Containers API against a real account.
 *
 * Only requests without side effects are made: GETs and the registry lookups,
 * which use POST.
 */
#[CoversNothing]
final class MagicContainersReadOnlyTest extends IntegrationTestCase
{
    public function testAccountWideLists(): void
    {
        $mc = self::client();

        $regions = $mc->regions->list(limit: 10);
        self::assertContainsOnlyInstancesOf(Region::class, $regions->items);
        self::assertCount($regions->totalItems ?? 0, iterator_to_array($mc->regions->all(limit: 10), false));
        self::assertNotSame('', $mc->regions->optimal()->id);

        self::assertSame($mc->nodes->plain(), iterator_to_array($mc->nodes->all(), false));
        self::assertContainsOnlyInstancesOf(ContainerRegistry::class, $mc->registries->list());
        $mc->limits->get();
        $mc->logForwarding->list();

        try {
            $mc->nodes->list(limit: 0);
            self::fail('Expected a BadRequestException.');
        } catch (BadRequestException $e) {
            self::assertSame('limit', $e->field);
        }
    }

    public function testRegistryLookups(): void
    {
        $registries = self::client()->registries;

        self::assertNotEmpty($registries->searchPublicImages('dockerhub', 'nginx'));
        self::assertContains('latest', array_map(static fn(ContainerImageTag $tag): string => $tag->name, $registries->tags('dockerhub', 'nginx', 'library')));
        self::assertStringStartsWith('sha256:', $registries->digest('dockerhub', 'nginx', 'library', 'latest')->digest);
        self::assertNotEmpty($registries->imageConfig('dockerhub', 'nginx', 'library', 'latest')->endpointSuggestions);
        self::assertNotEmpty($registries->configSuggestions('dockerhub', 'nginx', 'library', 'latest')->endpointSuggestions);

        try {
            $registries->images('dockerhub');
            self::fail('Expected a NotFoundException.');
        } catch (NotFoundException) {
            // Listing images needs a registry with stored credentials.
        }
    }

    public function testApplications(): void
    {
        $mc = self::client();
        $page = $mc->apps->list(limit: 10);

        self::assertContainsOnlyInstancesOf(ApplicationListItem::class, $page->items);

        if ($page->items === []) {
            self::markTestSkipped('The account has no applications.');
        }

        $id = $page->items[0]->id;
        $app = $mc->apps->get($id);
        self::assertSame($id, $app->id);

        $mc->apps->summary($id);
        $mc->apps->overview($id);
        $mc->apps->autoscaling($id);
        $mc->apps->regionSettings($id);
        $mc->endpoints->list($id);
        $mc->volumes->list($id);

        foreach ($app->containerTemplates as $container) {
            self::assertSame($container->id, $mc->containers->get($id, $container->id)->id);
        }
    }

    private static function client(): MagicContainersClient
    {
        return new Bunny(self::apiKey())->magicContainers;
    }
}
