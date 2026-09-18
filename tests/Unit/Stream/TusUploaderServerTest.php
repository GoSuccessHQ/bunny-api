<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Stream;

use GoSuccess\Bunny\Exception\ForbiddenException;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Http\CurlHttpClient;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Request;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Stream\Upload\TusSession;
use GoSuccess\Bunny\Stream\Upload\TusUpload;
use GoSuccess\Bunny\Stream\Upload\TusUploader;
use GoSuccess\Bunny\Tests\Support\FakeClock;
use GoSuccess\Bunny\Tests\Support\LocalServer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Uploads over real HTTP to the minimal TUS server of the local test server.
 */
#[CoversClass(TusUploader::class)]
final class TusUploaderServerTest extends TestCase
{
    private const int CHUNK = 64 * 1024;

    private static ?LocalServer $server = null;

    public static function setUpBeforeClass(): void
    {
        self::$server = new LocalServer();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server?->stop();
        self::$server = null;

        foreach (glob(sys_get_temp_dir() . '/bunny-api-tus-test/*') ?: [] as $file) {
            unlink($file);
        }
    }

    public function testUploadsAFileInChunks(): void
    {
        $data = random_bytes(5 * self::CHUNK + 1234);
        $progress = [];

        $session = $this->uploader()->upload(
            'v-1',
            Stream::fromString($data),
            'video/mp4',
            'Clip',
            onProgress: static function (int $sent) use (&$progress): void {
                $progress[] = $sent;
            },
        );

        self::assertTrue($session->isComplete());
        self::assertCount(6, $progress);
        self::assertSame(hash('sha256', $data), $this->received($session)['sha256']);
        self::assertSame(TusUpload::metadata('video/mp4', 'Clip'), $this->received($session)['metadata']);
    }

    public function testRecoversWhenTheConnectionBreaksMidChunk(): void
    {
        $clock = new FakeClock();
        $data = random_bytes(4 * self::CHUNK);

        $session = $this->uploader('interrupt', $clock)->upload('v-1', $data, 'video/mp4', 'Clip');

        self::assertSame([0.0], $clock->sleeps);
        self::assertSame(hash('sha256', $data), $this->received($session)['sha256']);
    }

    public function testResumesInAnotherProcess(): void
    {
        $data = random_bytes(4 * self::CHUNK);
        $path = (string) tempnam(sys_get_temp_dir(), 'tus');
        file_put_contents($path, $data);
        $stored = $this->uploader('break')->create('v-1', \strlen($data), 'video/mp4', 'Clip')->toArray();

        try {
            $this->uploader()->resume(TusSession::fromArray($stored), Stream::fromFile($path));
            self::fail('Expected a ForbiddenException.');
        } catch (ForbiddenException) {
            // The third chunk was rejected; two arrived.
        }

        $session = $this->uploader()->resume(TusSession::fromArray($stored), Stream::fromFile($path));
        unlink($path);

        self::assertTrue($session->isComplete());
        self::assertSame(hash('sha256', $data), $this->received($session)['sha256']);
    }

    public function testAbortsAnUpload(): void
    {
        $uploader = $this->uploader();
        $session = $uploader->create('v-1', 100, 'video/mp4', 'Clip');

        $uploader->abort($session);

        $this->expectException(NotFoundException::class);
        $uploader->status($session);
    }

    private function uploader(string $scenario = '', FakeClock $clock = new FakeClock()): TusUploader
    {
        self::assertNotNull(self::$server);

        return new TusUploader(42, 'library-key', chunkSize: self::CHUNK, endpoint: self::$server->baseUri . "/tus/{$scenario}", clock: $clock);
    }

    /**
     * @return array<array-key, mixed>
     */
    private function received(TusSession $session): array
    {
        $response = new CurlHttpClient()->send(new Request(Method::Get, $session->url, [
            'Tus-Resumable' => '1.0.0',
            ...TusUpload::presign(42, 'library-key', 'v-1', $session->expires)->headers,
        ]));
        $decoded = json_decode($response->body, true);
        self::assertIsArray($decoded);

        return $decoded;
    }
}
