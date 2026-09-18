<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Exception;

use GoSuccess\Bunny\Exception\ApiException;
use GoSuccess\Bunny\Exception\ErrorDetails;
use GoSuccess\Bunny\Exception\RateLimitException;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Request;
use GoSuccess\Bunny\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ApiException::class)]
#[CoversClass(ErrorDetails::class)]
#[CoversClass(RateLimitException::class)]
final class ApiExceptionTest extends TestCase
{
    /**
     * @param non-empty-string|null $message
     */
    #[DataProvider('errorFormats')]
    public function testNormalizesTheErrorFormatsOfAllApis(string $body, ?string $message, ?string $errorKey, ?string $field): void
    {
        $exception = ApiException::fromResponse(new Response(400, $body), $this->request());

        self::assertSame($errorKey, $exception->errorKey);
        self::assertSame($field, $exception->field);
        self::assertSame($body, $exception->responseBody);

        if ($message !== null) {
            self::assertStringEndsWith($message . ($errorKey !== null ? " ({$errorKey})" : ''), $exception->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string, non-empty-string|null, string|null, string|null}>
     */
    public static function errorFormats(): iterable
    {
        yield 'core' => ['{"ErrorKey":"pullZone.not_found","Field":"PullZone","Message":"Not found"}', 'Not found', 'pullZone.not_found', 'PullZone'];
        yield 'core message only' => ['{"Message":"The request is invalid."}', 'The request is invalid.', null, null];
        yield 'storage' => ['{"HttpCode":404,"Message":"Object Not Found"}', 'Object Not Found', null, null];
        yield 'stream' => ['{"success":false,"message":"Video not found","statusCode":404}', 'Video not found', null, null];
        yield 'logging' => ['{"error":{"code":"forbidden","message":"No access","details":null}}', 'No access', 'forbidden', null];
        yield 'problem details' => ['{"type":"x","title":"Not Found","status":404,"detail":"App missing"}', 'App missing', null, null];
        yield 'shield envelope' => ['{"data":null,"error":{"success":false,"message":"Zone missing","errorKey":"shield.zone"}}', 'Zone missing', 'shield.zone', null];
        yield 'shield errorResponse' => ['{"logs":null,"errorResponse":{"success":false,"message":"Rule missing","errorKey":"not_found.waf_rule"}}', 'Rule missing', 'not_found.waf_rule', null];
        yield 'plain text' => ['Bad Gateway', 'Bad Gateway', null, null];
        yield 'empty' => ['', null, null, null];
    }

    public function testShortensHugeBodiesWithoutBreakingUtf8(): void
    {
        $body = str_repeat('ä', 400);
        $exception = ApiException::fromResponse(new Response(502, $body), $this->request());

        self::assertTrue(mb_check_encoding($exception->getMessage(), 'UTF-8'));
        self::assertStringEndsWith('…', $exception->getMessage());
        self::assertLessThan(600, \strlen($exception->getMessage()));
    }

    public function testDecodesTheBody(): void
    {
        $exception = ApiException::fromResponse(new Response(400, '{"Message":"x"}'), $this->request());

        self::assertSame(['Message' => 'x'], $exception->decodedBody());
    }

    public function testParsesRetryAfter(): void
    {
        self::assertSame(30, RateLimitException::parseRetryAfter('30'));
        self::assertNull(RateLimitException::parseRetryAfter(''));
        self::assertNull(RateLimitException::parseRetryAfter('soon'));
        self::assertSame(0, RateLimitException::parseRetryAfter('Wed, 21 Oct 2015 07:28:00 GMT'));

        $future = gmdate('D, d M Y H:i:s', time() + 120) . ' GMT';
        self::assertGreaterThanOrEqual(118, RateLimitException::parseRetryAfter($future));
    }

    public function testRateLimitExceptionCarriesRetryAfter(): void
    {
        $exception = ApiException::fromResponse(new Response(429, '', ['retry-after' => '7']), $this->request());

        self::assertInstanceOf(RateLimitException::class, $exception);
        self::assertSame(7, $exception->retryAfter);
    }

    private function request(): Request
    {
        return new Request(Method::Get, 'https://api.bunny.net/pullzone');
    }
}
