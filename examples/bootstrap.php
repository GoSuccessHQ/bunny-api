<?php

declare(strict_types=1);

/**
 * Shared bootstrap of the example scripts.
 *
 * Every example only reads data. Set your account API key before running one:
 *
 *   BUNNY_API_KEY=your-api-key php examples/core-pull-zones.php
 */

use GoSuccess\Bunny\Bunny;

require __DIR__ . '/../vendor/autoload.php';

$apiKey = getenv('BUNNY_API_KEY');

if (!is_string($apiKey) || $apiKey === '') {
    fwrite(STDERR, "Please set the BUNNY_API_KEY environment variable.\n");
    exit(1);
}

return new Bunny($apiKey);
