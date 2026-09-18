<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Model;

use DateTimeImmutable;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Model\Json;
use GoSuccess\Bunny\Tests\Support\ExampleIntEnum;
use GoSuccess\Bunny\Tests\Support\ExampleModel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Cast::class)]
#[CoversClass(Json::class)]
final class CastTest extends TestCase
{
    public function testConvertsScalarsLeniently(): void
    {
        self::assertSame('abc', Cast::string('abc'));
        self::assertSame('5', Cast::string(5));
        self::assertNull(Cast::string(true));
        self::assertNull(Cast::string(null));

        self::assertSame(5, Cast::int(5));
        self::assertSame(5, Cast::int(5.0));
        self::assertSame(-12, Cast::int('-12'));
        self::assertNull(Cast::int(5.5));
        self::assertNull(Cast::int('5.5'));
        self::assertNull(Cast::int('abc'));

        self::assertSame(1.5, Cast::float(1.5));
        self::assertSame(2.0, Cast::float(2));
        self::assertSame(0.25, Cast::float('0.25'));
        self::assertNull(Cast::float('x'));

        self::assertTrue(Cast::bool(true));
        self::assertTrue(Cast::bool('true'));
        self::assertFalse(Cast::bool(0));
        self::assertFalse(Cast::bool('False'));
        self::assertNull(Cast::bool('yes'));
    }

    public function testParsesDatesAsUtcWhenTheZoneIsMissing(): void
    {
        self::assertSame('2026-09-17T06:20:32+00:00', Cast::dateTime('2026-09-17T06:20:32')?->format(\DATE_ATOM));
        self::assertSame('2026-09-17T06:20:32+02:00', Cast::dateTime('2026-09-17T06:20:32+02:00')?->format(\DATE_ATOM));
        // .NET sends seven fractional digits; PHP keeps microseconds.
        self::assertSame('2026-09-17T06:20:32.123456+00:00', Cast::dateTime('2026-09-17T06:20:32.1234567Z')?->format('Y-m-d\TH:i:s.uP'));
        self::assertNull(Cast::dateTime('not a date'));
        self::assertNull(Cast::dateTime(''));
        self::assertNull(Cast::dateTime(1_700_000_000));
    }

    public function testConvertsMillisecondTimestamps(): void
    {
        self::assertSame('2024-10-15T00:27:45.848+00:00', Cast::timestampMs(1728952065848)?->format('Y-m-d\TH:i:s.vP'));
        self::assertSame('1969-12-31T23:59:59.500+00:00', Cast::timestampMs(-500)?->format('Y-m-d\TH:i:s.vP'));
        self::assertNull(Cast::timestampMs('soon'));
    }

    public function testConvertsEnumsWithoutTypeErrors(): void
    {
        self::assertSame(ExampleIntEnum::Two, Cast::intEnum(ExampleIntEnum::class, 2));
        self::assertSame(ExampleIntEnum::Two, Cast::intEnum(ExampleIntEnum::class, '2'));
        self::assertNull(Cast::intEnum(ExampleIntEnum::class, 99));
        self::assertNull(Cast::intEnum(ExampleIntEnum::class, 'two'));

        self::assertSame(Method::Get, Cast::stringEnum(Method::class, 'GET'));
        self::assertNull(Cast::stringEnum(Method::class, 'TRACE'));
        self::assertNull(Cast::stringEnum(Method::class, null));
    }

    public function testConvertsModelsListsAndMaps(): void
    {
        self::assertSame('a', Cast::model(ExampleModel::class, ['Name' => 'a'])?->name);
        self::assertNull(Cast::model(ExampleModel::class, 'a'));

        $models = Cast::modelList(ExampleModel::class, [['Name' => 'a'], 'junk', ['Name' => 'b']]);
        self::assertSame(['a', 'b'], array_map(static fn(ExampleModel $model): ?string => $model->name, $models));
        self::assertSame([], Cast::modelList(ExampleModel::class, null));

        self::assertSame(['a', '5'], Cast::listOf(['a', 5, null, true], Cast::string(...)));
        self::assertSame([], Cast::listOf('scalar', Cast::string(...)));

        // Numeric JSON object keys arrive as integers.
        self::assertSame([1 => 5, 28 => 7, 'x' => 1], Cast::mapOf(['1' => 5, '28' => 7, 'x' => 1, 'y' => 'no'], Cast::int(...)));

        self::assertSame(['a' => 1], Cast::object(['a' => 1]));
        self::assertNull(Cast::object('x'));
    }

    public function testFormatsPayloadValues(): void
    {
        self::assertSame('2026-09-18T12:30:00Z', Json::date(new DateTimeImmutable('2026-09-18T14:30:00+02:00')));
        self::assertSame('0.1', Json::float(0.1));
        self::assertEquals(new stdClass(), Json::map([]));
        self::assertSame(['a' => 1], Json::map(['a' => 1]));
    }
}
