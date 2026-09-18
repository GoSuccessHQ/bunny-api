<?php

declare(strict_types=1);

/**
 * Generate the reference pages under docs/ from the client and resource classes.
 *
 * Usage:
 *   php tools/generate-docs.php
 *
 * Run it after tools/generate.php. Pages that are no longer produced are
 * deleted; docs/ contains generated pages only.
 */

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\OriginErrors\OriginErrorsClient;
use GoSuccess\Bunny\Storage\StorageClient;
use GoSuccess\Bunny\Storage\StorageRegion;
use GoSuccess\Bunny\Tools\Generator\Docs\DocSection;
use GoSuccess\Bunny\Tools\Generator\Docs\DocsGenerator;
use GoSuccess\Bunny\Tools\Generator\Docs\DocTarget;
use GoSuccess\Bunny\Tools\Generator\Generator;

require __DIR__ . '/../vendor/autoload.php';

const SETUP = "\$bunny = new Bunny('your-api-key');";

/**
 * The APIs in documentation order. Generated APIs are described by their
 * generator configuration; hand-written ones list their classes here.
 *
 * @var list<string|DocSection> $apis
 */
$apis = [
    'core',
    new DocSection('origin-errors', 'Origin Errors API', OriginErrorsClient::class, '$bunny->originErrors', [Bunny::class], SETUP, [
        new DocTarget(null, OriginErrorsClient::class, 'Requests the CDN could not complete because the origin failed.'),
    ]),
    'logging',
    new DocSection('storage', 'Edge Storage API', StorageClient::class, '$storage', [Bunny::class, StorageRegion::class], SETUP . "\n\$storage = \$bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);", [
        new DocTarget(null, StorageClient::class, 'Files and directories of one storage zone.'),
    ]),
    'stream',
];

/**
 * Generated APIs whose client is not a property of Bunny: how the examples
 * reach it.
 *
 * @var array<string, array{accessor: string, setup: string}> $entryPoints
 */
$entryPoints = [
    'stream' => ['accessor' => '$stream', 'setup' => SETUP . "\n\$stream = \$bunny->stream(12345, 'library-api-key');"],
];

$sections = [];

foreach ($apis as $api) {
    if ($api instanceof DocSection) {
        $sections[] = $api;

        continue;
    }

    $analysis = new Generator(__DIR__ . "/config/{$api}.php")->analyze();
    $config = $analysis->config;
    $targets = [];

    foreach ($analysis->resources as $resource) {
        $methods = [];

        foreach ($resource->config->methods as $name => $method) {
            $methods[] = $name;

            if ($method->all !== null) {
                $methods[] = $method->all;
            }
        }

        $targets[] = new DocTarget($resource->config->property, $resource->class, $resource->config->description, $methods);
    }

    $client = "GoSuccess\\Bunny\\{$config->namespace}\\{$config->client}";
    $entryPoint = $entryPoints[$config->name] ?? ['accessor' => "\$bunny->{$config->name}", 'setup' => SETUP];
    $sections[] = new DocSection($config->name, $config->title, $client, $entryPoint['accessor'], [Bunny::class], $entryPoint['setup'], $targets);
}

$docs = dirname(__DIR__) . '/docs';
$files = new DocsGenerator($sections)->render();
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
