<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\Stream;

use DateTimeImmutable;
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\VideoLibrary;
use GoSuccess\Bunny\Stream\Model\Collection;
use GoSuccess\Bunny\Stream\Model\Video;
use GoSuccess\Bunny\Stream\StreamClient;
use GoSuccess\Bunny\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Read-only checks of the Stream API against a real account.
 *
 * Uses the library's read-only API key, which cannot modify anything, except
 * for the one GET request the API only answers for the full key.
 */
#[CoversNothing]
final class StreamReadOnlyTest extends IntegrationTestCase
{
    private static ?VideoLibrary $library = null;

    public function testVideos(): void
    {
        $stream = self::client();
        $page = $stream->videos->list(itemsPerPage: 10);

        self::assertContainsOnlyInstancesOf(Video::class, $page->items);
        self::assertCount($page->totalItems ?? 0, iterator_to_array($stream->videos->all(), false));

        if ($page->items === []) {
            self::markTestSkipped('The video library has no videos.');
        }

        $video = $stream->videos->get($page->items[0]->guid);
        self::assertSame($page->items[0]->guid, $video->guid);
        self::assertSame($stream->libraryId, $video->videoLibraryId);

        $stream->videos->playData($video->guid);
        $stream->videos->resolutions($video->guid);
        $stream->videos->heatmap($video->guid);
        $stream->videos->playHeatmap($video->guid);
        $stream->statistics->get(videoGuid: $video->guid);
        self::client(readOnly: false)->videos->storageSize($video->guid);

        $embed = "https://iframe.mediadelivery.net/embed/{$stream->libraryId}/{$video->guid}";
        self::assertNotEmpty($stream->videos->oEmbed($embed)->html);
    }

    public function testCollections(): void
    {
        $stream = self::client();
        $page = $stream->collections->list(itemsPerPage: 10);

        self::assertContainsOnlyInstancesOf(Collection::class, $page->items);

        if ($page->items === [] || $page->items[0]->guid === null) {
            self::markTestSkipped('The video library has no collections.');
        }

        self::assertSame($page->items[0]->name, $stream->collections->get($page->items[0]->guid)->name);
    }

    public function testStatistics(): void
    {
        $statistics = self::client()->statistics->get(dateFrom: new DateTimeImmutable('-7 days'), dateTo: new DateTimeImmutable());

        // Empty intervals are reported as zero, so a week is never empty.
        self::assertNotEmpty($statistics->viewsChart);
    }

    private static function client(bool $readOnly = true): StreamClient
    {
        $bunny = new Bunny(self::apiKey());

        if (self::$library === null) {
            $libraries = $bunny->core->videoLibraries->list(perPage: 10)->items;

            if ($libraries === []) {
                self::markTestSkipped('The account has no video libraries.');
            }

            self::$library = $bunny->core->videoLibraries->get($libraries[0]->id);
        }

        return $bunny->streamFor(self::$library, $readOnly);
    }
}
