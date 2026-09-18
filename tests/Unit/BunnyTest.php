<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit;

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\CoreClient;
use GoSuccess\Bunny\EdgeScripting\EdgeScriptingClient;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Logging\LoggingClient;
use GoSuccess\Bunny\MagicContainers\MagicContainersClient;
use GoSuccess\Bunny\OriginErrors\OriginErrorsClient;
use GoSuccess\Bunny\Shield\ShieldClient;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bunny::class)]
final class BunnyTest extends TestCase
{
    public function testCreatesEachClientOnceAndSharesTheTransport(): void
    {
        $http = new MockHttpClient(new Response(200, '{"Count":3}'));
        $bunny = new Bunny('account-key', httpClient: $http);

        self::assertInstanceOf(CoreClient::class, $bunny->core);
        self::assertSame($bunny->core, $bunny->core);
        self::assertSame(3, $bunny->core->pullZones->count());
        self::assertSame('account-key', $http->requests[0]->headers['AccessKey']);
        self::assertInstanceOf(OriginErrorsClient::class, $bunny->originErrors);
        self::assertInstanceOf(LoggingClient::class, $bunny->logging);
        self::assertInstanceOf(ShieldClient::class, $bunny->shield);
        self::assertInstanceOf(EdgeScriptingClient::class, $bunny->edgeScripting);
        self::assertInstanceOf(MagicContainersClient::class, $bunny->magicContainers);
    }

    public function testHidesTheApiKeyFromDumps(): void
    {
        self::assertStringNotContainsString('account-key', print_r(new Bunny('account-key'), true));
    }
}
