<?php

declare(strict_types=1);

/**
 * List the videos and collections of the first video library and last week's
 * views, using the library's read-only API key.
 */

use GoSuccess\Bunny\Bunny;

/** @var Bunny $bunny */
$bunny = require __DIR__ . '/bootstrap.php';

$libraries = $bunny->core->videoLibraries->list()->items;

if ($libraries === []) {
    exit("The account has no video libraries.\n");
}

// get() returns the library including its API keys; the read-only one cannot modify anything.
$stream = $bunny->streamFor($bunny->core->videoLibraries->get($libraries[0]->id), readOnly: true);

echo "Video library {$stream->libraryId}:\n";

foreach ($stream->videos->all() as $video) {
    printf(
        "  %-40s %-12s %8s %8d views\n",
        $video->title,
        $video->status->name ?? 'unknown',
        gmdate('H:i:s', $video->length),
        $video->views,
    );
}

foreach ($stream->collections->all() as $collection) {
    echo "  Collection {$collection->name}: {$collection->videoCount} videos\n";
}

$statistics = $stream->statistics->get(dateFrom: new DateTimeImmutable('-7 days'));
$views = array_sum($statistics->viewsChart);

echo "Views in the last 7 days: {$views}\n";
