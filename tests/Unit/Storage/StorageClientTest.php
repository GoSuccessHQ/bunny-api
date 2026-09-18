<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Storage;

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\StorageZone;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Storage\Model\StorageObject;
use GoSuccess\Bunny\Storage\StorageClient;
use GoSuccess\Bunny\Storage\StorageRegion;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StorageClient::class)]
#[CoversClass(StorageObject::class)]
#[CoversClass(StorageRegion::class)]
final class StorageClientTest extends TestCase
{
    private const string OBJECT = '{"Guid":"g-1","StorageZoneName":"my-zone","Path":"/my-zone/images/","ObjectName":"logo.png","Length":1234,"LastChanged":"2026-07-25T15:00:47.123","ServerId":7,"ArrayNumber":2,"IsDirectory":false,"UserId":"u-1","ContentType":"","DateCreated":"2026-07-25T15:00:47","StorageZoneId":42,"Checksum":"ABC123","ReplicatedZones":"SE,NY"}';

    public function testListsADirectory(): void
    {
        $http = new MockHttpClient(new Response(200, '[' . self::OBJECT . ']'));

        $objects = $this->client($http)->list('/images');

        self::assertSame('https://storage.bunnycdn.com/my-zone/images/', $http->requests[0]->uri);
        self::assertSame('zone-password', $http->requests[0]->headers['AccessKey']);
        self::assertCount(1, $objects);

        $object = $objects[0];
        self::assertSame('logo.png', $object->objectName);
        self::assertSame('images/logo.png', $object->relativePath);
        self::assertSame(1234, $object->length);
        self::assertSame(['SE', 'NY'], $object->replicatedZones);
        self::assertSame('ABC123', $object->checksum);
        self::assertSame('2026-07-25T15:00:47.123+00:00', $object->lastChanged?->format('Y-m-d\TH:i:s.vP'));
    }

    public function testListsTheRoot(): void
    {
        $http = new MockHttpClient(new Response(200, '[]'));

        self::assertSame([], $this->client($http)->list());
        self::assertSame('https://storage.bunnycdn.com/my-zone/', $http->requests[0]->uri);
    }

    public function testDescribesAnObjectAndChecksExistence(): void
    {
        $http = new MockHttpClient(new Response(200, self::OBJECT), new Response(404, '{"HttpCode":404,"Message":"Object Not Found"}'));
        $client = $this->client($http);

        self::assertSame('g-1', $client->describe('images/logo.png')->guid);
        self::assertSame(Method::Describe, $http->requests[0]->method);
        self::assertSame('https://storage.bunnycdn.com/my-zone/images/logo.png', $http->requests[0]->uri);

        self::assertFalse($client->exists('missing.txt'));
    }

    public function testReportsMissingFilesAsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Object Not Found');

        $this->client(new MockHttpClient(new Response(404, '{"HttpCode":404,"Message":"Object Not Found"}')))->get('missing.txt');
    }

    public function testDownloadsIntoMemoryAndIntoStreams(): void
    {
        $http = new MockHttpClient(new Response(200, 'file-contents'), new Response(200, 'streamed'));
        $client = $this->client($http);

        self::assertSame('file-contents', $client->get('a b/ä.txt'));
        self::assertSame('https://storage.bunnycdn.com/my-zone/a%20b/%C3%A4.txt', $http->requests[0]->uri);

        $target = Stream::temporary();
        $client->download('file.bin', $target);
        rewind($target->resource);
        self::assertSame('streamed', $target->contents());
        self::assertSame($target, $http->requests[1]->sink);
    }

    public function testUploadsWithChecksumAndContentType(): void
    {
        $http = new MockHttpClient(new Response(201), new Response(201), new Response(201), new Response(201));
        $client = $this->client($http);

        $client->upload('docs/readme.txt', 'hello', contentType: 'text/plain');
        $request = $http->requests[0];
        self::assertSame(Method::Put, $request->method);
        self::assertSame('https://storage.bunnycdn.com/my-zone/docs/readme.txt', $request->uri);
        self::assertSame('application/octet-stream', $request->headers['Content-Type']);
        self::assertSame('text/plain', $request->headers['Override-Content-Type']);
        self::assertSame(strtoupper(hash('sha256', 'hello')), $request->headers['Checksum']);
        self::assertSame('hello', $http->bodies[0]);

        // A seekable stream is hashed without being consumed.
        $client->upload('big.bin', Stream::fromString('stream-data'));
        self::assertSame(strtoupper(hash('sha256', 'stream-data')), $http->requests[1]->headers['Checksum']);
        self::assertSame('stream-data', $http->bodies[1]);

        // A non-seekable stream is buffered first.
        $pair = stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
        self::assertIsArray($pair);
        fwrite($pair[1], 'piped');
        fclose($pair[1]);
        $client->upload('piped.bin', new Stream($pair[0]));
        self::assertSame(strtoupper(hash('sha256', 'piped')), $http->requests[2]->headers['Checksum']);
        self::assertSame('piped', $http->bodies[2]);

        $client->upload('raw.bin', 'x', verifyChecksum: false);
        self::assertArrayNotHasKey('Checksum', $http->requests[3]->headers);
    }

    public function testManagesDirectories(): void
    {
        $http = new MockHttpClient(new Response(201), new Response(200), new Response(200), new Response(200));
        $client = $this->client($http);

        $client->createDirectory('backups/2026');
        $client->delete('old.txt');
        $client->deleteDirectory('backups');
        $client->deleteDirectory('/', allowRoot: true);

        self::assertSame([Method::Put, Method::Delete, Method::Delete, Method::Delete], array_map(static fn($request) => $request->method, $http->requests));
        self::assertSame('https://storage.bunnycdn.com/my-zone/backups/2026/', $http->requests[0]->uri);
        self::assertSame('https://storage.bunnycdn.com/my-zone/old.txt', $http->requests[1]->uri);
        self::assertSame('https://storage.bunnycdn.com/my-zone/backups/', $http->requests[2]->uri);
        self::assertSame('https://storage.bunnycdn.com/my-zone/?allowRootDelete=true', $http->requests[3]->uri);
    }

    public function testRefusesToDeleteTheRootByAccident(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->client(new MockHttpClient())->deleteDirectory('/');
    }

    public function testRejectsRelativeSegments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->client(new MockHttpClient())->get('../other-zone/file.txt');
    }

    public function testUsesTheRegionHost(): void
    {
        $http = new MockHttpClient(new Response(200, '[]'));

        new StorageClient('my-zone', 'pw', StorageRegion::NewYork, httpClient: $http)->list();

        self::assertSame('https://ny.storage.bunnycdn.com/my-zone/', $http->requests[0]->uri);
        self::assertSame(StorageRegion::Sydney, StorageRegion::fromCode('SYD'));
        self::assertSame('storage.bunnycdn.com', StorageRegion::Falkenstein->host());
    }

    public function testBunnyCreatesClientsFromCoreStorageZones(): void
    {
        $http = new MockHttpClient(new Response(200, '[]'), new Response(200, '[]'));
        $bunny = new Bunny('account-key', httpClient: $http);
        $zone = new StorageZone(name: 'my-zone', password: 'rw', region: 'UK', readOnlyPassword: 'ro', storageHostname: 'uk.storage.bunnycdn.com');

        $bunny->storageFor($zone)->list();
        $bunny->storageFor($zone, readOnly: true)->list();

        self::assertSame('https://uk.storage.bunnycdn.com/my-zone/', $http->requests[0]->uri);
        self::assertSame('rw', $http->requests[0]->headers['AccessKey']);
        self::assertSame('ro', $http->requests[1]->headers['AccessKey']);
    }

    public function testHidesThePasswordFromDumps(): void
    {
        self::assertStringNotContainsString('zone-password', print_r($this->client(new MockHttpClient()), true));
    }

    private function client(MockHttpClient $http): StorageClient
    {
        return new StorageClient('my-zone', 'zone-password', httpClient: $http);
    }
}
