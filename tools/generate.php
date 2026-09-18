<?php

declare(strict_types=1);

/**
 * Generate the enums, models, resources and clients of the bunny.net APIs from
 * the committed specification snapshots (resources/specs/) and the generator
 * configuration (tools/config/).
 *
 * Usage:
 *   php tools/generate.php              # all configured APIs
 *   php tools/generate.php core stream  # selected APIs
 *
 * Generated files carry a marker comment; files with the marker that are no
 * longer produced are deleted, hand-written files are never touched.
 */

use GoSuccess\Bunny\Tools\Generator\Generator;

require __DIR__ . '/../vendor/autoload.php';

$arguments = is_array($_SERVER['argv'] ?? null) ? array_slice($_SERVER['argv'], 1) : [];
$selected = array_values(array_filter($arguments, is_string(...)));
$configs = glob(__DIR__ . '/config/*.php') ?: [];
$exitCode = 0;

foreach ($configs as $configFile) {
    $name = basename($configFile, '.php');

    if ($selected !== [] && !in_array($name, $selected, true)) {
        continue;
    }

    try {
        $report = new Generator($configFile)->run();
        echo $report;
    } catch (Throwable $e) {
        fwrite(STDERR, "[{$name}] {$e->getMessage()}\n");
        $exitCode = 1;
    }
}

exit($exitCode);
