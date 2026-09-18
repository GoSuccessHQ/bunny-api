<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Http;

use DateTimeImmutable;
use DateTimeZone;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Query;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Query::class)]
final class QueryTest extends TestCase
{
    public function testFormatsValuesTheWayTheApisExpect(): void
    {
        $query = Query::build([
            'page' => 2,
            'includeCertificate' => true,
            'hourly' => false,
            'ratio' => 0.5,
            'search' => 'a b/c&d',
            'method' => Method::Post,
            'dateFrom' => new DateTimeImmutable('2026-09-18 14:30:00', new DateTimeZone('Europe/Berlin')),
            'skipped' => null,
        ]);

        self::assertSame(
            'page=2&includeCertificate=true&hourly=false&ratio=0.5&search=a%20b%2Fc%26d&method=POST&dateFrom=2026-09-18T12%3A30%3A00Z',
            $query,
        );
    }

    public function testSendsListsAsRepeatedKeys(): void
    {
        self::assertSame('type=0&type=1&type=2', Query::build(['type' => [0, 1, null, 2]]));
    }

    public function testReturnsAnEmptyStringWithoutParameters(): void
    {
        self::assertSame('', Query::build(['a' => null]));
    }

    public function testFormatsFloatsWithoutExponent(): void
    {
        self::assertSame('x=0.00001&y=1000000&z=0', Query::build(['x' => 0.00001, 'y' => 1e6, 'z' => -0.0]));
    }

    public function testRejectsUnsupportedValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Query::build(['object' => new stdClass()]);
    }
}
