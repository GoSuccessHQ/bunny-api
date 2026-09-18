<?php

declare(strict_types=1);

/**
 * Generate the reference pages under docs/ from the resource classes.
 *
 * Usage:
 *   php tools/generate-docs.php
 *
 * Run it after tools/generate.php. Pages that are no longer produced are
 * deleted; docs/ contains generated pages only.
 */

use GoSuccess\Bunny\Tools\Generator\Docs\DocsGenerator;
use GoSuccess\Bunny\Tools\Generator\Generator;

require __DIR__ . '/../vendor/autoload.php';

/** How each API client is reached from the Bunny entry point. */
const ACCESSORS = [
    'core' => '$bunny->core',
];

$apis = [];

foreach (glob(__DIR__ . '/config/*.php') ?: [] as $file) {
    $analysis = new Generator($file)->analyze();
    $apis[$analysis->config->name] = $analysis;
}

$docs = dirname(__DIR__) . '/docs';
$files = new DocsGenerator($apis, ACCESSORS)->render();
$written = 0;

foreach ($files as $path => $content) {
    $target = "{$docs}/{$path}";

    if (!is_dir(dirname($target))) {
        mkdir(dirname($target), 0o775, true);
    }

    if (!is_file($target) || file_get_contents($target) !== $content) {
        file_put_contents($target, $content);
        ++$written;
    }
}

$deleted = 0;

if (is_dir($docs)) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($docs, FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file instanceof SplFileInfo && $file->getExtension() === 'md' && !isset($files[substr($file->getPathname(), strlen($docs) + 1)])) {
            unlink($file->getPathname());
            ++$deleted;
        }
    }
}

echo count($files) . " pages, {$written} written, {$deleted} deleted\n";
