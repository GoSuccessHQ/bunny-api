<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Support\Generated;

use GoSuccess\Bunny\Tools\Generator\Analysis;
use GoSuccess\Bunny\Tools\Generator\Generator;

/**
 * The generator's view of every configured API, built once per test run.
 */
final class GeneratedApis
{
    /** @var array<string, Analysis>|null */
    private static ?array $apis = null;

    /**
     * @return array<string, Analysis>
     */
    public static function all(): array
    {
        if (self::$apis !== null) {
            return self::$apis;
        }

        $apis = [];

        foreach (glob(\dirname(__DIR__, 3) . '/tools/config/*.php') ?: [] as $file) {
            $analysis = new Generator($file)->analyze();
            $apis[$analysis->config->name] = $analysis;
        }

        return self::$apis = $apis;
    }
}
