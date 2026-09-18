<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Stream;

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\VideoLibrary;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Stream\Pagination;
use GoSuccess\Bunny\Stream\StreamClient;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pagination::class)]
final class HandwrittenOperationsTest extends TestCase
{
    public function testUploadStreamsTheFileWithEncodingOptions(): void
    {
        $http = new MockHttpClient(new Response(200, '{"success":true,"message":"OK","statusCode":200}'));

        $this->client($http)->videos->upload('v-1', Stream::fromString('mp4-bytes'), enabledResolutions: ['720p', '1080p'], transcribeEnabled: true);

        $request = $http->requests[0];
        self::assertSame(Method::Put, $request->method);
        self::assertSame('https://video.bunnycdn.com/library/42/videos/v-1?enabledResolutions=720p%2C1080p&transcribeEnabled=true', $request->uri);
        self::assertSame('application/octet-stream', $request->headers['Content-Type']);
        self::assertSame('library-key', $request->headers['AccessKey']);
        self::assertSame('mp4-bytes', $http->bodies[0]);
    }

    public function testFetchReturnsTheGuidOfTheNewVideo(): void
    {
        $http = new MockHttpClient(
            new Response(200, '{"success":true,"message":"OK","statusCode":200,"id":"v-9"}'),
            new Response(200, '{"success":true,"message":"OK","statusCode":200}'),
        );
        $videos = $this->client($http)->videos;

        self::assertSame('v-9', $videos->fetch('https://example.com/a.mp4', title: 'A', collectionId: 'c-1', headers: ['Authorization' => 'Bearer x']));
        self::assertSame(Method::Post, $http->requests[0]->method);
        self::assertSame('https://video.bunnycdn.com/library/42/videos/fetch?collectionId=c-1', $http->requests[0]->uri);
        self::assertSame(['url' => 'https://example.com/a.mp4', 'title' => 'A', 'headers' => ['Authorization' => 'Bearer x']], $http->jsonBody());

        self::assertNull($videos->fetch('https://example.com/b.mp4', headers: []));
        self::assertSame('{"url":"https://example.com/b.mp4","headers":{}}', $http->bodies[1]);
    }

    public function testSetsThumbnailsInThreeWays(): void
    {
        $http = new MockHttpClient(new Response(200), new Response(200), new Response(200));
        $videos = $this->client($http)->videos;

        $videos->setThumbnail('v-1', 'https://example.com/thumb.jpg');
        $videos->useGeneratedThumbnail('v-1', 3);
        $videos->uploadThumbnail('v-1', 'png-bytes', 'image/png');

        self::assertSame(Method::Post, $http->requests[0]->method);
        self::assertSame('https://video.bunnycdn.com/library/42/videos/v-1/thumbnail?thumbnailUrl=https%3A%2F%2Fexample.com%2Fthumb.jpg', $http->requests[0]->uri);
        self::assertSame('https://video.bunnycdn.com/library/42/videos/v-1/thumbnail?thumbnailUrl=thumbnail_3.jpg', $http->requests[1]->uri);
        self::assertSame('https://video.bunnycdn.com/library/42/videos/v-1/thumbnail', $http->requests[2]->uri);
        self::assertSame('png-bytes', $http->bodies[2]);
        self::assertSame('image/png', $http->requests[2]->headers['Content-Type']);
    }

    public function testRejectsUnknownGeneratedThumbnails(): void
    {
        $http = new MockHttpClient();

        try {
            $this->client($http)->videos->useGeneratedThumbnail('v-1', 6);
            self::fail('Expected an InvalidArgumentException.');
        } catch (InvalidArgumentException) {
            self::assertSame([], $http->requests);
        }
    }

    public function testAddCaptionEncodesTheFileAndUnwrapsTheValidation(): void
    {
        $http = new MockHttpClient(new Response(200, '{"success":true,"statusCode":200,"data":{"valid":true,"warningList":["line 3"]}}'));
        $captions = "WEBVTT\n\n00:00.000 --> 00:01.000\nHi";

        $validation = $this->client($http)->videos->addCaption('v-1', 'en', Stream::fromString($captions), label: 'English');

        self::assertSame('https://video.bunnycdn.com/library/42/videos/v-1/captions/en', $http->requests[0]->uri);
        self::assertSame(['srclang' => 'en', 'captionsFile' => base64_encode($captions), 'label' => 'English'], $http->jsonBody());
        self::assertNotNull($validation);
        self::assertTrue($validation->valid);
        self::assertSame(['line 3'], $validation->warningList);
    }

    public function testAddCaptionToleratesAPlainStatus(): void
    {
        $http = new MockHttpClient(new Response(200, '{"success":true,"message":"OK","statusCode":200}'));

        self::assertNull($this->client($http)->videos->addCaption('v-1', 'de', 'WEBVTT'));
        self::assertSame(['srclang' => 'de', 'captionsFile' => base64_encode('WEBVTT')], $http->jsonBody());
    }

    public function testPlayHeatmapReturnsTheRawBody(): void
    {
        $http = new MockHttpClient(new Response(200, ''));

        self::assertSame('', $this->client($http)->videos->playHeatmap('v-1', token: 'abc', expires: 1700000000));
        self::assertSame('https://video.bunnycdn.com/library/42/videos/v-1/play/heatmap?token=abc&expires=1700000000', $http->requests[0]->uri);
    }

    public function testPagesByTheEffectivePageSize(): void
    {
        // Asked for 5 items per page; the API clamps that to 10.
        $http = new MockHttpClient(
            new Response(200, $this->page(1, 10, 25)),
            new Response(200, $this->page(2, 10, 25)),
            new Response(200, $this->page(3, 5, 25)),
        );

        $videos = iterator_to_array($this->client($http)->videos->all(itemsPerPage: 5), false);

        self::assertCount(25, $videos);
        self::assertCount(3, $http->requests);
        self::assertSame('https://video.bunnycdn.com/library/42/videos?page=3&itemsPerPage=5', $http->requests[2]->uri);
    }

    public function testStopsAtAnEmptyPage(): void
    {
        self::assertNull(Pagination::page(['currentPage' => 1, 'itemsPerPage' => 10, 'totalItems' => 25], [])->next);
        self::assertNull(Pagination::page(['currentPage' => 1, 'itemsPerPage' => 10], ['x'])->next);
    }

    public function testBunnyCreatesClientsFromCoreVideoLibraries(): void
    {
        $http = new MockHttpClient(new Response(200, $this->page(1, 0, 0)), new Response(200, $this->page(1, 0, 0)));
        $bunny = new Bunny('account-key', httpClient: $http);
        $library = new VideoLibrary(id: 42, apiKey: 'rw', readOnlyApiKey: 'ro');

        $bunny->streamFor($library)->videos->list();
        $bunny->streamFor($library, readOnly: true)->collections->list();

        self::assertSame('https://video.bunnycdn.com/library/42/videos?page=1&itemsPerPage=100', $http->requests[0]->uri);
        self::assertSame('rw', $http->requests[0]->headers['AccessKey']);
        self::assertSame('https://video.bunnycdn.com/library/42/collections?page=1&itemsPerPage=100', $http->requests[1]->uri);
        self::assertSame('ro', $http->requests[1]->headers['AccessKey']);
    }

    public function testRejectsVideoLibrariesWithoutKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Bunny('account-key', httpClient: new MockHttpClient())->streamFor(new VideoLibrary(id: 42));
    }

    public function testHidesTheApiKeyFromDumps(): void
    {
        self::assertStringNotContainsString('library-key', print_r($this->client(new MockHttpClient()), true));
    }

    private function client(MockHttpClient $http): StreamClient
    {
        return new StreamClient(42, 'library-key', httpClient: $http);
    }

    private function page(int $page, int $count, int $total): string
    {
        $items = implode(',', array_fill(0, $count, '{"guid":"v"}'));

        return "{\"items\":[{$items}],\"currentPage\":{$page},\"itemsPerPage\":10,\"totalItems\":{$total}}";
    }
}
