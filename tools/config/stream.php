<?php

declare(strict_types=1);

/**
 * Generator configuration of the Stream API.
 */
return [
    'name' => 'stream',
    'title' => 'Stream API',
    'spec' => 'stream',
    'namespace' => 'Stream',
    'client' => 'StreamClient',
    'baseUri' => 'https://video.bunnycdn.com',
    'credential' => 'apiKey',
    'credentialDescription' => 'The API key of the video library (dashboard → Stream → library → API), not the account API key.',

    // Every path but /OEmbed starts with /library/{libraryId}: the client takes
    // the library once instead of every method.
    'clientParameters' => [
        'libraryId' => ['type' => 'int', 'description' => 'The ID of the video library.'],
    ],

    // {"success", "message", "statusCode"} carries no payload; errors arrive as
    // HTTP error statuses (bunny.net's Terraform provider relies on the status
    // code alone, too).
    'voidResponses' => ['StatusModel'],

    'schemas' => [
        'EncoderOutputCodec' => 'OutputCodec',
        'IssueCodes' => 'TranscodingIssue',
        'Severity' => 'TranscodingSeverity',
        // The envelope of addCaption(), which is hand-written and unwraps it.
        'StatusModelOfCaptionValidationModel' => false,
        'StorageObjectModel' => 'VideoStorageObject',
        'UpdateVideoModel' => 'VideoUpdate',
        'VideoModelStatus' => 'VideoStatus',
    ],

    'properties' => [],

    'enumCases' => [
        'EncoderOutputCodec' => [0 => 'X264', 1 => 'VP9', 2 => 'HEVC', 3 => 'AV1'],
    ],

    // Returned by the hand-written videos->addCaption().
    'extraModels' => ['CaptionValidationModel'],

    'pagination' => [
        // {"items", "currentPage", "itemsPerPage", "totalItems"}. The API clamps
        // itemsPerPage to 10…1000 (verified live).
        'page' => [
            'position' => 'page',
            'positionType' => 'int',
            'first' => 1,
            'size' => 'itemsPerPage',
            'pageSize' => 100,
            'allSize' => 1000,
            'items' => 'items',
            'factory' => 'Pagination::page',
        ],
    ],

    'resources' => [
        'videos' => [
            'class' => 'VideoResource',
            'description' => 'Videos of the library: uploads, metadata, captions, thumbnails, encoding and playback data.',
            'handwritten' => true,
            'methods' => [
                'list' => ['operation' => 'Video_List', 'pagination' => 'page', 'all' => 'all'],
                'get' => ['operation' => 'Video_GetVideo'],
                'create' => ['operation' => 'Video_CreateVideo', 'flatten' => true],
                'update' => ['operation' => 'Video_UpdateVideo', 'parameters' => ['@body' => 'changes']],
                'delete' => ['operation' => 'Video_DeleteVideo'],
                // A raw binary body with encoding options in the query.
                'upload' => ['operation' => 'Video_UploadVideo', 'handwritten' => true],
                // Documented as a bare status, but the response also carries the new
                // video's GUID as "id" (relied on by bunny.net's CLI, BunnyWay/cli#224).
                'fetch' => ['operation' => 'Video_FetchNewVideo', 'handwritten' => true],
                // One operation, three ways: a URL, one of the generated thumbnails or an image upload.
                'setThumbnail' => ['operation' => 'Video_SetThumbnail', 'handwritten' => true],
                'useGeneratedThumbnail' => ['operation' => 'Video_SetThumbnail', 'handwritten' => true],
                'uploadThumbnail' => ['operation' => 'Video_SetThumbnail', 'handwritten' => true],
                // The captions file travels base64-encoded; the method takes it as is.
                'addCaption' => ['operation' => 'Video_AddCaption', 'handwritten' => true],
                'deleteCaption' => ['operation' => 'Video_DeleteCaption'],
                'transcribe' => ['operation' => 'Video_TranscribeVideo', 'flatten' => true],
                'smartGenerate' => ['operation' => 'Video_SmartGenerate', 'flatten' => true],
                'reencode' => ['operation' => 'Video_ReencodeVideo'],
                'addOutputCodec' => ['operation' => 'Video_ReencodeUsingCodec'],
                'repackage' => ['operation' => 'Video_Repackage'],
                'resolutions' => ['operation' => 'Video_GetVideoResolutions', 'unwrap' => 'data'],
                'cleanupResolutions' => ['operation' => 'Video_DeleteResolutions', 'unwrap' => 'data'],
                'storageSize' => [
                    'operation' => 'Video_GetVideoStorageSize',
                    'unwrap' => 'data',
                    // Verified live: the read-only key gets 403 here, unlike every other GET.
                    'note' => 'Requires the full API key of the library; the read-only key is rejected with 403 Forbidden.',
                ],
                'heatmap' => ['operation' => 'Video_GetVideoHeatmap'],
                'playData' => ['operation' => 'Video_GetVideoPlayData'],
                // Documented as raw heatmap data or an empty string; the specification
                // wrongly reuses the play data schema.
                'playHeatmap' => ['operation' => 'Video_GetVideoHeatmapData', 'handwritten' => true],
                // Optional in the specification, but the endpoint needs a player URL.
                'oEmbed' => ['operation' => 'OEmbed_GetOEmbed', 'required' => ['url']],
            ],
        ],
        'collections' => [
            'class' => 'CollectionResource',
            'description' => 'Collections that group the videos of the library.',
            'methods' => [
                'list' => ['operation' => 'Collection_List', 'pagination' => 'page', 'all' => 'all'],
                'get' => ['operation' => 'Collection_GetCollection'],
                'create' => ['operation' => 'Collection_CreateCollection', 'flatten' => true],
                'update' => ['operation' => 'Collection_UpdateCollection', 'flatten' => true],
                'delete' => ['operation' => 'Collection_DeleteCollection'],
            ],
        ],
        'statistics' => [
            'class' => 'StatisticsResource',
            'description' => 'View and watch time statistics of the library or of one video.',
            'methods' => [
                'get' => ['operation' => 'Video_GetVideoStatistics'],
            ],
        ],
    ],

    'ignored' => [],
];
