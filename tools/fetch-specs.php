<?php

declare(strict_types=1);

/**
 * Refresh the committed snapshots of the official bunny.net API specifications.
 *
 * Every specification is downloaded from its canonical URL and stored
 * pretty-printed under resources/specs/, so that a refresh produces a readable
 * diff. The JSON is decoded into objects (not arrays) to keep empty objects
 * (`{}`) and empty lists (`[]`) apart.
 *
 * Usage:
 *   php tools/fetch-specs.php            # refresh all snapshots
 *   php tools/fetch-specs.php core shield # refresh selected snapshots
 */

const SPEC_DIR = __DIR__ . '/../resources/specs';

const SPECS = [
    'core' => 'https://core-api-public-docs.b-cdn.net/docs/v3/public.json',
    'origin-errors' => 'https://bunny.net/docs/api-reference/origin-errors/openapi.json',
    'logging' => 'https://logging.bunnycdn.com/docs/all/swagger.json',
    'storage' => 'https://bunny.net/docs/api-reference/storage/openapi.json',
    'stream' => 'https://video.bunnycdn.com/openapi/bunnynet-video-api.public.json',
    'shield' => 'https://api.bunny.net/shield/docs/v1/swagger.json',
    'edge-scripting' => 'https://core-api-public-docs.b-cdn.net/docs/v3/compute.json',
    'magic-containers' => 'https://api-mc.opsbunny.net/docs/public/swagger.json',
];

$arguments = is_array($_SERVER['argv'] ?? null) ? array_slice($_SERVER['argv'], 1) : [];
$selected = array_values(array_filter($arguments, is_string(...)));
$unknown = array_diff($selected, array_keys(SPECS));

if ($unknown !== []) {
    fwrite(STDERR, 'Unknown specification(s): ' . implode(', ', $unknown) . "\n");
    exit(1);
}

if (!is_dir(SPEC_DIR) && !mkdir(SPEC_DIR, 0o775, true)) {
    fwrite(STDERR, 'Cannot create ' . SPEC_DIR . "\n");
    exit(1);
}

foreach (SPECS as $name => $url) {
    if ($selected !== [] && !in_array($name, $selected, true)) {
        continue;
    }

    $raw = file_get_contents($url);

    if ($raw === false) {
        fwrite(STDERR, "Failed to download {$url}\n");
        exit(1);
    }

    $decoded = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
    $json = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);

    file_put_contents(SPEC_DIR . "/{$name}.json", "{$json}\n");
    echo "wrote resources/specs/{$name}.json\n";
}
