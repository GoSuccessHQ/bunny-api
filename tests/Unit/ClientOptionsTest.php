<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit;

use GoSuccess\Bunny\ClientOptions;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClientOptions::class)]
final class ClientOptionsTest extends TestCase
{
    public function testHasSensibleDefaults(): void
    {
        $options = new ClientOptions();

        self::assertSame(30.0, $options->timeout);
        self::assertSame(0.0, $options->transferTimeout);
        self::assertSame(3, $options->maxRetries);
    }

    /**
     * @param array{timeout?: float, connectTimeout?: float, transferTimeout?: float, maxRetries?: int, retryBaseDelay?: float, maxRetryDelay?: float, userAgent?: string} $arguments
     */
    #[DataProvider('invalidOptions')]
    public function testRejectsInvalidValues(array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ClientOptions(...$arguments);
    }

    /**
     * @return iterable<string, array{array{timeout?: float, connectTimeout?: float, transferTimeout?: float, maxRetries?: int, retryBaseDelay?: float, maxRetryDelay?: float, userAgent?: string}}>
     */
    public static function invalidOptions(): iterable
    {
        yield 'negative timeout' => [['timeout' => -1.0]];
        yield 'negative connect timeout' => [['connectTimeout' => -1.0]];
        yield 'negative transfer timeout' => [['transferTimeout' => -1.0]];
        yield 'negative retries' => [['maxRetries' => -1]];
        yield 'negative base delay' => [['retryBaseDelay' => -0.5]];
        yield 'negative max delay' => [['maxRetryDelay' => -0.5]];
        yield 'empty user agent' => [['userAgent' => ' ']];
    }
}
