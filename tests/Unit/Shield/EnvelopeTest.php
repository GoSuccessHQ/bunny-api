<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Shield;

use GoSuccess\Bunny\Exception\ApiException;
use GoSuccess\Bunny\Exception\BadRequestException;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Shield\Envelope;
use GoSuccess\Bunny\Shield\ShieldClient;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Envelope::class)]
final class EnvelopeTest extends TestCase
{
    private const string PLAN_ERROR = '{"data":null,"error":{"statusCode":202,"success":false,"message":"Unable to make changes whilst on the Basic tier of Bunny Shield.","errorKey":"invalid_plan_type.bot_detection"}}';

    /**
     * @return iterable<string, array{string, int|null}>
     */
    public static function bodies(): iterable
    {
        yield 'plan restriction' => [self::PLAN_ERROR, 202];
        yield 'not found' => ['{"data":null,"error":{"statusCode":202,"success":false,"message":"Shield Zone not found or Unauthorized access.","errorKey":"not_found_or_unauthorised_access.shieldzone"}}', 404];
        yield 'not found in errorResponse' => ['{"id":0,"errorResponse":{"statusCode":202,"success":false,"message":"Rule not found.","errorKey":"not_found.waf_rule"}}', 404];
        yield 'error status in the body' => ['{"statusCode":400,"success":false,"message":"Invalid rule.","errorKey":null}', 400];
        yield 'spaced JSON' => ['{ "data": null, "error": { "success": false, "message": "Nope" } }', 202];
        yield 'successful envelope' => ['{"data":{"shieldZoneId":1},"error":null}', null];
        yield 'successful status' => ['{"statusCode":200,"success":true,"message":"Deleted.","errorKey":null}', null];
        yield 'empty default' => ['{"data":{"id":1},"errorResponse":{"statusCode":0,"success":false,"message":null,"errorKey":null}}', null];
        yield 'nested payload' => ['{"data":{"success":false,"message":"part of the data"},"error":null}', null];
        yield 'not JSON' => ['<html>success:false</html>', null];
        yield 'empty' => ['', null];
    }

    #[DataProvider('bodies')]
    public function testClassifiesErrorsInSuccessfulResponses(string $body, ?int $expected): void
    {
        self::assertSame($expected, Envelope::errorStatus(new Response(202, $body)));
    }

    public function testTurnsAnAcceptedErrorIntoAnException(): void
    {
        $http = new MockHttpClient(new Response(202, self::PLAN_ERROR, reasonPhrase: 'Accepted'));

        try {
            new ShieldClient('key', httpClient: $http)->botDetection->get(42);
            self::fail('Expected an ApiException.');
        } catch (ApiException $e) {
            self::assertSame(ApiException::class, $e::class);
            self::assertSame(202, $e->statusCode);
            self::assertSame('invalid_plan_type.bot_detection', $e->errorKey);
            self::assertStringContainsString('Unable to make changes whilst on the Basic tier', $e->getMessage());
            self::assertCount(1, $http->requests);
        }
    }

    public function testReportsAcceptedNotFoundAsNotFound(): void
    {
        $http = new MockHttpClient(new Response(202, '{"id":0,"shieldZoneId":0,"ruleConfiguration":null,"errorResponse":{"statusCode":202,"success":false,"message":"Rule not found or Unauthorized access.","errorKey":"not_found_or_unauthorised_access.waf_rule"}}'));

        try {
            new ShieldClient('key', httpClient: $http)->rateLimits->get(42);
            self::fail('Expected a NotFoundException.');
        } catch (NotFoundException $e) {
            self::assertSame(202, $e->statusCode);
            self::assertSame('not_found_or_unauthorised_access.waf_rule', $e->errorKey);
        }
    }

    public function testKeepsRegularErrorStatuses(): void
    {
        $http = new MockHttpClient(new Response(400, '{"statusCode":400,"success":false,"message":"Custom response pages are not available on the Basic plan.","errorKey":"feature_not_available_on_plan"}'));

        $this->expectException(BadRequestException::class);

        new ShieldClient('key', httpClient: $http)->uploadScanning->get(42);
    }
}
