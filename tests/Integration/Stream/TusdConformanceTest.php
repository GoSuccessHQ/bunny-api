<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\Stream;

use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Http\CurlHttpClient;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Request;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Stream\Upload\TusSession;
use GoSuccess\Bunny\Stream\Upload\TusUploader;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Checks the TUS client against tusd, the reference implementation of the
 * protocol. Skipped unless TUSD_ENDPOINT points to a tusd server:
 *
 *   docker run --rm -p 127.0.0.1:1080:8080 tusproject/tusd
 *   TUSD_ENDPOINT=http://127.0.0.1:1080/files/ composer test:integration
 *
 * tusd does not support the checksum extension, so checksums stay off here.
 */
#[CoversNothing]
final class TusdConformanceTest extends TestCase
{
    private const int CHUNK = 1024 * 1024;

    public function testUploadsALargeFileInChunks(): void
    {
        $data = random_bytes((int) (3.5 * self::CHUNK));

        $session = $this->uploader()->upload('v-1', Stream::fromString($data), 'video/mp4', 'Clip');

        self::assertTrue($session->isComplete());
        self::assertSame(hash('sha256', $data), hash('sha256', $this->download($session)));
    }

    public function testResumesAfterACrash(): void
    {
        $data = random_bytes(4 * self::CHUNK);
        $stored = $this->uploader()->create('v-1', \strlen($data), 'video/mp4', 'Clip')->toArray();

        try {
            $this->uploader()->resume(TusSession::fromArray($stored), $data, static function (int $sent): void {
                if ($sent >= 2 * self::CHUNK) {
                    throw new RuntimeException('Simulated crash');
                }
            });
            self::fail('Expected the simulated crash.');
        } catch (RuntimeException $e) {
            self::assertSame('Simulated crash', $e->getMessage());
        }

        $status = $this->uploader()->status(TusSession::fromArray($stored));
        self::assertSame(2 * self::CHUNK, $status->offset);

        $session = $this->uploader()->resume(TusSession::fromArray($stored), $data);

        self::assertTrue($session->isComplete());
        self::assertSame(hash('sha256', $data), hash('sha256', $this->download($session)));
    }

    public function testAbortsAnUpload(): void
    {
        $uploader = $this->uploader();
        $session = $uploader->create('v-1', 100, 'video/mp4', 'Clip');

        $uploader->abort($session);

        $this->expectException(NotFoundException::class);
        $uploader->status($session);
    }

    private function uploader(): TusUploader
    {
        $endpoint = getenv('TUSD_ENDPOINT');

        if (!\is_string($endpoint) || $endpoint === '') {
            self::markTestSkipped('Set TUSD_ENDPOINT to check the TUS client against tusd.');
        }

        return new TusUploader(42, 'library-key', chunkSize: self::CHUNK, checksums: false, endpoint: $endpoint);
    }

    private function download(TusSession $session): string
    {
        return new CurlHttpClient()->send(new Request(Method::Get, $session->url, ['Tus-Resumable' => '1.0.0']))->body;
    }
}
