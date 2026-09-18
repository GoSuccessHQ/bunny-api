<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Http;

use GoSuccess\Bunny\Http\Stream;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(Stream::class)]
final class StreamTest extends TestCase
{
    public function testDetectsTheRemainingSizeOfSeekableStreams(): void
    {
        $stream = Stream::fromString('hello world');
        self::assertSame(11, $stream->size);
        self::assertTrue($stream->isSeekable);

        fseek($stream->resource, 6);
        self::assertSame(5, new Stream($stream->resource)->size);
    }

    public function testKeepsTheSizeOfSocketsUnknown(): void
    {
        $pair = stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
        self::assertIsArray($pair);
        fwrite($pair[1], 'data');

        $stream = new Stream($pair[0]);
        self::assertNull($stream->size);
        self::assertFalse($stream->isSeekable);
    }

    public function testOpensFiles(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bunny');
        self::assertIsString($path);
        file_put_contents($path, 'abc');

        try {
            $stream = Stream::fromFile($path);
            self::assertSame(3, $stream->size);
            self::assertSame('abc', $stream->contents());
        } finally {
            unlink($path);
        }
    }

    public function testRejectsMissingFiles(): void
    {
        $this->expectException(RuntimeException::class);

        Stream::fromFile('/nonexistent/file');
    }

    public function testRejectsNonStreams(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Stream(stream_context_create());
    }

    public function testRejectsNegativeSizes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Stream(Stream::temporary()->resource, -1);
    }
}
