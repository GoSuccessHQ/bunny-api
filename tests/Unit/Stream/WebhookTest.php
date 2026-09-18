<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Stream;

use GoSuccess\Bunny\Exception\InvalidSignatureException;
use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Stream\Webhook\WebhookEvent;
use GoSuccess\Bunny\Stream\Webhook\WebhookSignature;
use GoSuccess\Bunny\Stream\Webhook\WebhookStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WebhookSignature::class)]
#[CoversClass(WebhookEvent::class)]
final class WebhookTest extends TestCase
{
    private const string BODY = '{"VideoLibraryId":133,"VideoGuid":"657bb740-a71b-4529-a012-528021c31a92","Status":3}';

    // HMAC-SHA256 of the body keyed with "read-only-key", computed with openssl.
    private const string SIGNATURE = '95ba721a9a8ee56efa4ce19d45dba902747c0effc76c76a6d3d316f1f332cfd8';

    public function testVerifiesTheSignature(): void
    {
        self::assertTrue(WebhookSignature::verify(self::BODY, self::SIGNATURE, 'read-only-key'));
    }

    public function testRejectsForgedOrUnknownSignatures(): void
    {
        $body = self::BODY;

        self::assertFalse(WebhookSignature::verify(self::BODY, self::SIGNATURE, 'other-key'));
        self::assertFalse(WebhookSignature::verify("{$body} ", self::SIGNATURE, 'read-only-key'));
        self::assertFalse(WebhookSignature::verify(self::BODY, strtoupper(self::SIGNATURE), 'read-only-key'));
        self::assertFalse(WebhookSignature::verify(self::BODY, '', 'read-only-key'));
        self::assertFalse(WebhookSignature::verify(self::BODY, self::SIGNATURE, 'read-only-key', version: 'v2'));
        self::assertFalse(WebhookSignature::verify(self::BODY, self::SIGNATURE, 'read-only-key', algorithm: 'hmac-sha512'));
    }

    public function testParsesAVerifiedCallWithAnyHeaderCase(): void
    {
        $event = WebhookSignature::parse(self::BODY, [
            'x-bunnystream-signature' => self::SIGNATURE,
            'X-BUNNYSTREAM-SIGNATURE-VERSION' => 'v1',
            'X-BunnyStream-Signature-Algorithm' => ['hmac-sha256'],
        ], 'read-only-key');

        self::assertSame(133, $event->libraryId);
        self::assertSame('657bb740-a71b-4529-a012-528021c31a92', $event->videoId);
        self::assertSame(WebhookStatus::Finished, $event->status);
        self::assertSame(3, $event->statusCode);
    }

    public function testRejectsUnsignedCalls(): void
    {
        $this->expectException(InvalidSignatureException::class);

        WebhookSignature::parse(self::BODY, ['X-BunnyStream-Signature' => self::SIGNATURE], 'read-only-key');
    }

    public function testKeepsUnknownStatusCodes(): void
    {
        $event = WebhookEvent::fromJson('{"VideoLibraryId":1,"VideoGuid":"x","Status":99}');

        self::assertNull($event->status);
        self::assertSame(99, $event->statusCode);
    }

    public function testRejectsIncompleteBodies(): void
    {
        $this->expectException(SerializationException::class);

        WebhookEvent::fromJson('{"VideoLibraryId":1,"Status":3}');
    }
}
