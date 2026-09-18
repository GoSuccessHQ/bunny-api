<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Stream;

use DateTimeImmutable;
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\VideoLibrary;
use GoSuccess\Bunny\Exception\AuthenticationException;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Exception\ServerException;
use GoSuccess\Bunny\Exception\TransportException;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Stream\Upload\TusSession;
use GoSuccess\Bunny\Stream\Upload\TusUpload;
use GoSuccess\Bunny\Stream\Upload\TusUploader;
use GoSuccess\Bunny\Tests\Support\FakeClock;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TusUploader::class)]
#[CoversClass(TusSession::class)]
final class TusUploaderTest extends TestCase
{
    private const string UPLOAD_URL = 'https://video.bunnycdn.com/tusupload/abc123';

    public function testUploadsTheFileInSignedChunksWithChecksums(): void
    {
        $http = new MockHttpClient(
            self::created(self::UPLOAD_URL),
            self::accepted(4),
            self::accepted(8),
            self::accepted(10),
        );
        $progress = [];
        $expires = new DateTimeImmutable('@1700000000');

        $session = $this->uploader($http)->upload(
            'v-1',
            Stream::fromString('abcdefghij'),
            'video/mp4',
            'Clip',
            expires: $expires,
            onProgress: static function (int $sent, int $total) use (&$progress): void {
                $progress[] = [$sent, $total];
            },
        );

        $create = $http->requests[0];
        self::assertSame(Method::Post, $create->method);
        self::assertSame('https://video.bunnycdn.com/tusupload', $create->uri);
        self::assertSame('1.0.0', $create->headers['Tus-Resumable']);
        self::assertSame('10', $create->headers['Upload-Length']);
        self::assertSame(TusUpload::metadata('video/mp4', 'Clip'), $create->headers['Upload-Metadata']);
        self::assertArrayNotHasKey('AccessKey', $create->headers);

        // Every request carries the signature, which bunny.net checks each time.
        $signature = TusUpload::presign(42, 'library-key', 'v-1', $expires)->headers;

        foreach ($http->requests as $request) {
            self::assertSame($signature['AuthorizationSignature'], $request->headers['AuthorizationSignature']);
            self::assertSame('1700000000', $request->headers['AuthorizationExpire']);
            self::assertSame('42', $request->headers['LibraryId']);
            self::assertSame('v-1', $request->headers['VideoId']);
        }

        self::assertSame(['abcd', 'efgh', 'ij'], \array_slice($http->bodies, 1));
        self::assertSame(Method::Patch, $http->requests[1]->method);
        self::assertSame(self::UPLOAD_URL, $http->requests[1]->uri);
        self::assertSame('0', $http->requests[1]->headers['Upload-Offset']);
        self::assertSame('8', $http->requests[3]->headers['Upload-Offset']);
        self::assertSame('application/offset+octet-stream', $http->requests[1]->headers['Content-Type']);
        self::assertSame('sha1 ' . base64_encode(sha1('abcd', true)), $http->requests[1]->headers['Upload-Checksum']);
        self::assertSame([[4, 10], [8, 10], [10, 10]], $progress);
        self::assertTrue($session->isComplete());
        self::assertSame(self::UPLOAD_URL, $session->url);
    }

    public function testResolvesRelativeLocations(): void
    {
        $http = new MockHttpClient(self::created('/tusupload/xyz'), self::created('xyz'));
        $uploader = $this->uploader($http);

        self::assertSame('https://video.bunnycdn.com/tusupload/xyz', $uploader->create('v-1', 10, 'video/mp4', 'Clip')->url);
        self::assertSame('https://video.bunnycdn.com/tusupload/xyz', $uploader->create('v-1', 10, 'video/mp4', 'Clip')->url);
    }

    public function testCatchesUpWithTheServerAfterAConnectionFailure(): void
    {
        $clock = new FakeClock();
        $http = new MockHttpClient(
            self::created(self::UPLOAD_URL),
            self::accepted(4),
            new TransportException('Connection reset'),
            self::offset(6),
            self::accepted(10),
        );

        $session = $this->uploader($http, clock: $clock)->upload('v-1', 'abcdefghij', 'video/mp4', 'Clip');

        self::assertSame(['POST', 'PATCH', 'PATCH', 'HEAD', 'PATCH'], array_map(static fn($request): string => $request->method->value, $http->requests));
        // Two bytes of the failed chunk arrived, so the upload goes on at 6.
        self::assertSame('6', $http->requests[4]->headers['Upload-Offset']);
        self::assertSame('ghij', $http->bodies[4]);
        self::assertSame([0.0], $clock->sleeps);
        self::assertTrue($session->isComplete());
    }

    public function testRetriesServerErrorsAndCorruptedChunks(): void
    {
        $clock = new FakeClock();
        $http = new MockHttpClient(
            self::created(self::UPLOAD_URL),
            new Response(503),
            self::offset(0),
            new Response(460, 'Header Upload-Checksum does not match the checksum of the file'),
            self::offset(0),
            self::accepted(4),
            new Response(500),
            self::offset(4),
            self::accepted(8),
        );

        $session = $this->uploader($http, clock: $clock)->upload('v-1', 'abcdefgh', 'video/mp4', 'Clip');

        // No progress between the first two failures; the third one starts over.
        self::assertSame([0.0, 3.0, 0.0], $clock->sleeps);
        self::assertSame(['abcd', 'abcd', 'abcd', 'efgh', 'efgh'], array_values(array_filter(\array_slice($http->bodies, 1), 'is_string')));
        self::assertTrue($session->isComplete());
    }

    public function testGivesUpAfterTheLastWait(): void
    {
        $clock = new FakeClock();
        $http = new MockHttpClient(
            self::created(self::UPLOAD_URL),
            new Response(503),
            self::offset(0),
            new Response(503),
            self::offset(0),
            new Response(503),
        );

        try {
            $this->uploader($http, clock: $clock, retryDelays: [0.0, 1.0])->upload('v-1', 'abcdefgh', 'video/mp4', 'Clip');
            self::fail('Expected a ServerException.');
        } catch (ServerException $e) {
            self::assertSame(503, $e->statusCode);
            self::assertSame([0.0, 1.0], $clock->sleeps);
        }
    }

    public function testStopsAtClientErrors(): void
    {
        $clock = new FakeClock();
        $http = new MockHttpClient(self::created(self::UPLOAD_URL), new Response(401, 'Invalid signature'));

        try {
            $this->uploader($http, clock: $clock)->upload('v-1', 'abcdefgh', 'video/mp4', 'Clip');
            self::fail('Expected an AuthenticationException.');
        } catch (AuthenticationException) {
            self::assertSame([], $clock->sleeps);
            self::assertCount(2, $http->requests);
        }
    }

    public function testResumesAStoredSession(): void
    {
        $http = new MockHttpClient(self::offset(8), self::accepted(10));
        $stored = new TusSession(self::UPLOAD_URL, 42, 'v-1', 10, 4, new DateTimeImmutable('@1700000000'))->toArray();

        $session = $this->uploader($http)->resume(TusSession::fromArray($stored), Stream::fromString('abcdefghij'));

        // The server knows best: it has 8 bytes, not the 4 stored.
        self::assertSame(Method::Head, $http->requests[0]->method);
        self::assertSame('8', $http->requests[1]->headers['Upload-Offset']);
        self::assertSame('ij', $http->bodies[1]);
        self::assertTrue($session->isComplete());
    }

    public function testRefusesFilesAndSessionsThatDoNotMatch(): void
    {
        $uploader = $this->uploader(new MockHttpClient());
        $session = new TusSession(self::UPLOAD_URL, 42, 'v-1', 10, 0, new DateTimeImmutable('@1700000000'));

        foreach ([
            fn() => $uploader->resume($session, 'too short'),
            fn() => $uploader->resume(new TusSession(self::UPLOAD_URL, 7, 'v-1', 10, 0, $session->expires), 'abcdefghij'),
            fn() => TusSession::fromArray(['url' => self::UPLOAD_URL]),
            fn() => new TusSession(self::UPLOAD_URL, 42, 'v-1', 10, 11, $session->expires),
        ] as $call) {
            try {
                $call();
                self::fail('Expected an InvalidArgumentException.');
            } catch (InvalidArgumentException) {
                self::addToAssertionCount(1);
            }
        }
    }

    public function testReadsAStreamFromItsPosition(): void
    {
        $http = new MockHttpClient(self::created(self::UPLOAD_URL), self::accepted(4), self::accepted(7));
        $file = Stream::fromString('---abcdefg');
        fseek($file->resource, 3);

        $this->uploader($http)->upload('v-1', new Stream($file->resource), 'video/mp4', 'Clip');

        self::assertSame('7', $http->requests[0]->headers['Upload-Length']);
        self::assertSame(['abcd', 'efg'], \array_slice($http->bodies, 1));
    }

    public function testChecksumsCanBeTurnedOff(): void
    {
        $http = new MockHttpClient(self::created(self::UPLOAD_URL), self::accepted(3));

        new TusUploader(42, 'library-key', httpClient: $http, checksums: false)->upload('v-1', 'abc', 'video/mp4', 'Clip');

        self::assertArrayNotHasKey('Upload-Checksum', $http->requests[1]->headers);
    }

    public function testReportsTheStatusAndAbortsUploads(): void
    {
        $http = new MockHttpClient(self::offset(6), new Response(204), new Response(404));
        $uploader = $this->uploader($http);
        $session = new TusSession(self::UPLOAD_URL, 42, 'v-1', 10, 0, new DateTimeImmutable('@1700000000'));

        self::assertSame(6, $uploader->status($session)->offset);
        $uploader->abort($session);
        self::assertSame(Method::Delete, $http->requests[1]->method);
        self::assertSame(self::UPLOAD_URL, $http->requests[1]->uri);

        // An expired upload is gone and cannot be resumed.
        $this->expectException(NotFoundException::class);
        $uploader->status($session);
    }

    public function testRejectsImplausibleOffsets(): void
    {
        foreach ([self::accepted(0), new Response(204), self::accepted(11)] as $response) {
            try {
                $this->uploader(new MockHttpClient(self::created(self::UPLOAD_URL), $response))->upload('v-1', 'abcdefghij', 'video/mp4', 'Clip');
                self::fail('Expected a SerializationException.');
            } catch (SerializationException) {
                self::addToAssertionCount(1);
            }
        }
    }

    public function testHidesTheApiKeyFromDumps(): void
    {
        self::assertStringNotContainsString('library-key', print_r($this->uploader(new MockHttpClient()), true));
    }

    public function testBunnyCreatesUploadersOnItsTransport(): void
    {
        $http = new MockHttpClient(self::created(self::UPLOAD_URL), self::accepted(3), self::created(self::UPLOAD_URL), self::accepted(3));
        $bunny = new Bunny('account-key', httpClient: $http);
        $expires = new DateTimeImmutable('@1700000000');
        $signature = TusUpload::presign(42, 'library-key', 'v-1', $expires)->headers['AuthorizationSignature'];

        $bunny->streamUploader(42, 'library-key')->upload('v-1', 'abc', 'video/mp4', 'Clip', expires: $expires);
        $bunny->streamUploaderFor(new VideoLibrary(id: 42, apiKey: 'library-key'))->upload('v-1', 'abc', 'video/mp4', 'Clip', expires: $expires);

        self::assertCount(4, $http->requests);
        self::assertSame($signature, $http->requests[0]->headers['AuthorizationSignature']);
        self::assertSame($signature, $http->requests[2]->headers['AuthorizationSignature']);

        $this->expectException(InvalidArgumentException::class);
        $bunny->streamUploaderFor(new VideoLibrary(id: 42));
    }

    /**
     * @param list<float> $retryDelays
     */
    private function uploader(MockHttpClient $http, FakeClock $clock = new FakeClock(), array $retryDelays = TusUploader::DEFAULT_RETRY_DELAYS): TusUploader
    {
        return new TusUploader(42, 'library-key', httpClient: $http, chunkSize: 4, retryDelays: $retryDelays, clock: $clock);
    }

    private static function created(string $location): Response
    {
        return new Response(201, '', ['location' => $location, 'tus-resumable' => '1.0.0']);
    }

    private static function accepted(int $offset): Response
    {
        return new Response(204, '', ['upload-offset' => (string) $offset, 'tus-resumable' => '1.0.0']);
    }

    private static function offset(int $offset): Response
    {
        return new Response(200, '', ['upload-offset' => (string) $offset, 'upload-length' => '10', 'tus-resumable' => '1.0.0']);
    }
}
