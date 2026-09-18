<?php

declare(strict_types=1);

/**
 * Generator configuration of the CDN Logging API.
 */
return [
    'name' => 'logging',
    'title' => 'CDN Logging API',
    'spec' => 'logging',
    'namespace' => 'Logging',
    'client' => 'LoggingClient',
    // The specification declares no server.
    'baseUri' => 'https://logging.bunnycdn.com',
    'credential' => 'apiKey',

    'schemas' => [],
    'properties' => [],
    'enumCases' => [],
    'extraModels' => [],

    'pagination' => [
        // {"data": [...], "pagination": {"offset", "limit", "returned", "hasMore"}}.
        // The API filters by country and search text after fetching a page, so a
        // page may be empty while more follow; the offset advances by the limit.
        'offset' => [
            'position' => 'offset',
            'positionType' => 'int',
            'first' => 0,
            'size' => 'limit',
            'pageSize' => 100,
            'allSize' => 1000,
            'items' => 'data',
            'factory' => 'Pagination::offset',
        ],
    ],

    'resources' => [
        'logs' => [
            'class' => 'LogResource',
            'description' => 'Raw CDN request logs of the last 3 days.',
            'handwritten' => true,
            'methods' => [
                'list' => [
                    'operation' => 'GET /v2/pullzones/{pullZoneId}/logs',
                    'pagination' => 'offset',
                    'all' => 'all',
                    'note' => 'The time range must lie within the last 3 days. bunny.net allows 30 requests per 10 seconds and pull zone.',
                ],
                // Pipe-delimited plain text, parsed by hand.
                'legacy' => ['operation' => 'GET /{date}/{pullZoneId}.log', 'handwritten' => true],
            ],
        ],
    ],

    'ignored' => [],
];
