<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Stream;

use DateTimeImmutable;
use GoSuccess\Bunny\Stream\Upload\TusUpload;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TusUpload::class)]
final class TusUploadTest extends TestCase
{
    public function testPresignsTheUpload(): void
    {
        $upload = TusUpload::presign(42, 'library-key', 'v-1', new DateTimeImmutable('@1700000000'));

        self::assertSame('https://video.bunnycdn.com/tusupload', $upload->endpoint);
        // SHA-256 of "42library-key1700000000v-1", computed with sha256sum.
        self::assertSame([
            'AuthorizationSignature' => 'd8955544996627cd6917acc5972dceb71a9580864d0b6de10f7abd3feebba032',
            'AuthorizationExpire' => '1700000000',
            'LibraryId' => '42',
            'VideoId' => 'v-1',
        ], $upload->headers);
        self::assertSame(1700000000, $upload->expires->getTimestamp());
    }

    public function testRejectsAnEmptyVideoId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TusUpload::presign(42, 'library-key', ' ', new DateTimeImmutable('+1 hour'));
    }

    public function testEncodesTheMetadata(): void
    {
        self::assertSame('filetype dmlkZW8vbXA0,title TXkgdmlkZW8=', TusUpload::metadata('video/mp4', 'My video'));
        self::assertSame(
            'filetype dmlkZW8vbXA0,title TXkgdmlkZW8=,collection Yy0x,thumbnailTime NTAwMA==',
            TusUpload::metadata('video/mp4', 'My video', collectionId: 'c-1', thumbnailTime: 5000),
        );
    }
}
