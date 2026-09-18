<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Stream;

use DateTimeImmutable;
use GoSuccess\Bunny\Stream\Security\EmbedToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmbedToken::class)]
final class EmbedTokenTest extends TestCase
{
    private const string KEY = '4742a81b-bf15-42fe-8b1c-8fcb9024c550';
    private const string VIDEO = '32d140e2-e4f4-4eec-9d53-20371e9be607';

    // SHA-256 of key, video GUID and expiry, computed with sha256sum.
    private const string TOKEN = 'a8617f6df2e9b55b65ac7112138c70417766d80614bfe146d0d9bb2bd21fef87';

    public function testSignsKeyVideoAndExpiry(): void
    {
        self::assertSame(self::TOKEN, EmbedToken::sign(self::KEY, self::VIDEO, new DateTimeImmutable('@1623440202')));
    }

    public function testBuildsTheSignedEmbedUrl(): void
    {
        $url = EmbedToken::url(759, self::VIDEO, self::KEY, new DateTimeImmutable('@1623440202'), [
            'autoplay' => true,
            'preload' => false,
            'token' => 'ignored',
        ]);

        $token = self::TOKEN;
        self::assertSame("https://iframe.mediadelivery.net/embed/759/32d140e2-e4f4-4eec-9d53-20371e9be607?autoplay=true&preload=false&token={$token}&expires=1623440202", $url);
    }
}
