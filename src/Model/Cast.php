<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Model;

use BackedEnum;
use Closure;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 * Type-safe conversion of loosely typed JSON values into PHP types, used by the
 * models to read API responses.
 *
 * Every method is lenient: a missing or mistyped value yields null (or an empty
 * list/map) instead of an error, so an API that changes a field cannot break
 * the client.
 *
 * @internal
 */
final class Cast
{
    private static ?DateTimeZone $utc = null;

    public static function string(mixed $value): ?string
    {
        return match (true) {
            \is_string($value) => $value,
            \is_int($value), \is_float($value) => (string) $value,
            default => null,
        };
    }

    public static function int(mixed $value): ?int
    {
        return match (true) {
            \is_int($value) => $value,
            \is_float($value) && is_finite($value) && $value === floor($value) => (int) $value,
            \is_string($value) && preg_match('/^-?\d+$/', $value) === 1 => (int) $value,
            default => null,
        };
    }

    public static function float(mixed $value): ?float
    {
        return match (true) {
            \is_float($value) => $value,
            \is_int($value) => (float) $value,
            \is_string($value) && is_numeric($value) => (float) $value,
            default => null,
        };
    }

    public static function bool(mixed $value): ?bool
    {
        return match ($value) {
            true, 1, '1', 'true', 'True' => true,
            false, 0, '0', 'false', 'False' => false,
            default => null,
        };
    }

    /**
     * Parse an ISO 8601 date. Values without a time zone, which several APIs
     * send, are interpreted as UTC.
     */
    public static function dateTime(mixed $value): ?DateTimeImmutable
    {
        if (!\is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value, self::$utc ??= new DateTimeZone('UTC'));
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $enum An int-backed enum.
     *
     * @return T|null Null for a missing value or a case this client does not know yet.
     */
    public static function intEnum(string $enum, mixed $value): ?BackedEnum
    {
        $int = self::int($value);

        return $int === null ? null : $enum::tryFrom($int);
    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $enum A string-backed enum.
     *
     * @return T|null Null for a missing value or a case this client does not know yet.
     */
    public static function stringEnum(string $enum, mixed $value): ?BackedEnum
    {
        $string = self::string($value);

        return $string === null ? null : $enum::tryFrom($string);
    }

    /**
     * @template T of ResponseModel
     *
     * @param class-string<T> $model
     *
     * @return T|null
     */
    public static function model(string $model, mixed $value): ?ResponseModel
    {
        return \is_array($value) ? $model::fromArray($value) : null;
    }

    /**
     * An untyped JSON object or array.
     *
     * @return array<array-key, mixed>|null
     */
    public static function object(mixed $value): ?array
    {
        return \is_array($value) ? $value : null;
    }

    /**
     * @template T of ResponseModel
     *
     * @param class-string<T> $model
     *
     * @return list<T>
     */
    public static function modelList(string $model, mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }

        $list = [];

        foreach ($value as $item) {
            if (\is_array($item)) {
                $list[] = $model::fromArray($item);
            }
        }

        return $list;
    }

    /**
     * @template T
     *
     * @param Closure(mixed): (T|null) $item Converts one element; null drops it.
     *
     * @return list<T>
     */
    public static function listOf(mixed $value, Closure $item): array
    {
        if (!\is_array($value)) {
            return [];
        }

        $list = [];

        foreach ($value as $element) {
            $converted = $item($element);

            if ($converted !== null) {
                $list[] = $converted;
            }
        }

        return $list;
    }

    /**
     * Keys are kept as they are; note that PHP turns numeric keys into integers.
     *
     * @template T
     *
     * @param Closure(mixed): (T|null) $item Converts one value; null drops the entry.
     *
     * @return array<array-key, T>
     */
    public static function mapOf(mixed $value, Closure $item): array
    {
        if (!\is_array($value)) {
            return [];
        }

        $map = [];

        foreach ($value as $key => $element) {
            $converted = $item($element);

            if ($converted !== null) {
                $map[$key] = $converted;
            }
        }

        return $map;
    }
}
