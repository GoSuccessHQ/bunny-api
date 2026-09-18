<?php

declare(strict_types=1);

/**
 * Generator configuration of the Edge Scripting API.
 */
return [
    'name' => 'edge-scripting',
    'title' => 'Edge Scripting API',
    'spec' => 'edge-scripting',
    'namespace' => 'EdgeScripting',
    'client' => 'EdgeScriptingClient',
    'baseUri' => 'https://api.bunny.net',
    'credential' => 'apiKey',

    'schemas' => [
        'EdgeScriptTypes' => 'EdgeScriptType',
        'EdgeScriptTypes2' => 'EdgeScriptType',
    ],

    'properties' => [],

    'enumCases' => [],

    'extraModels' => [],

    'pagination' => [
        // The page format of the Core API; pages start at 1, page 0 fails with
        // HTTP 500 (verified live).
        'page' => [
            'position' => 'page',
            'positionType' => 'int',
            'first' => 1,
            'size' => 'perPage',
            'pageSize' => 100,
            'allSize' => 1000,
            'items' => 'Items',
            'factory' => 'Core\\Pagination::page',
        ],
    ],

    'resources' => [
        'scripts' => [
            'class' => 'EdgeScriptResource',
            'description' => 'Edge scripts: their settings, code, statistics and deployment key.',
            'methods' => [
                'list' => [
                    'operation' => 'ListEdgeScriptsEndpoint_ListEdgeScriptsByAccount',
                    'pagination' => 'page',
                    'all' => 'all',
                    'parameters' => ['includeLinkedPullzones' => 'includeLinkedPullZones'],
                ],
                'get' => ['operation' => 'GetEdgeScriptByIdEndpoint_GetEdgeScriptById'],
                // A script without name or type makes no sense; the API would default the type to DNS.
                'create' => ['operation' => 'CreateEdgeScriptEndpoint_AddScript', 'flatten' => true, 'required' => ['Name', 'ScriptType']],
                'update' => ['operation' => 'UpdateEdgeScriptEndpoint_Update', 'flatten' => true],
                'delete' => ['operation' => 'DeleteEdgeScriptEndpoint_Delete'],
                'statistics' => ['operation' => 'EdgeScriptStatisticsEndpoint_GetEdgeScriptStatisticsEndpoint'],
                'code' => ['operation' => 'GetEdgeScriptCodeEndpoint_GetCode'],
                'setCode' => ['operation' => 'UploadEdgeScriptCodeEndpoint_SetCode', 'flatten' => true, 'required' => ['Code']],
                'rotateDeploymentKey' => ['operation' => 'RotateEdgeScriptDeploymentKeyEndpoint_RotateEdgeScriptDeploymentKey'],
            ],
        ],
        'releases' => [
            'class' => 'ReleaseResource',
            'description' => 'Releases of edge scripts: the published versions of their code.',
            'parameters' => ['id' => 'scriptId'],
            'methods' => [
                'list' => ['operation' => 'GetEdgeScriptReleaseEndpoint_GetReleases', 'pagination' => 'page', 'all' => 'all'],
                'active' => ['operation' => 'GetEdgeScriptActiveReleaseEndpoint_GetCurrentlyActiveReleaseEndpoint'],
                // Publishes the current code; the specification declares a stray {uuid} here.
                'publish' => ['operation' => 'PublishEdgeScriptReleaseEndpoint_Publish', 'flatten' => true],
                'publishRelease' => ['operation' => 'PublishEdgeScriptReleaseEndpoint_Publish2', 'flatten' => true],
            ],
        ],
        'variables' => [
            'class' => 'VariableResource',
            'description' => 'Environment variables of edge scripts.',
            'parameters' => ['id' => 'scriptId'],
            'methods' => [
                'get' => ['operation' => 'GetEdgeScriptVariableEndpoint_GetVariable'],
                'create' => ['operation' => 'AddEdgeScriptVariableEndpoint_AddEdgeScriptVariable', 'flatten' => true],
                'update' => ['operation' => 'UpdateEdgeScriptVariableEndpoint_UpdateVariable', 'flatten' => true],
                // Documented as 200 or 204 with a body, which 204 cannot carry.
                'upsert' => [
                    'operation' => 'UpsertEdgeScriptVariableEndpoint_UpsertEdgeScriptVariable',
                    'flatten' => true,
                    'nullable' => true,
                    'note' => 'Returns null if bunny.net answers 204 No Content, which the specification allows.',
                ],
                'delete' => ['operation' => 'DeleteEdgeScriptVariableEndpoint_DeleteVariable'],
            ],
        ],
        'secrets' => [
            'class' => 'SecretResource',
            'description' => 'Secrets of edge scripts; their values can be written, never read.',
            'parameters' => ['id' => 'scriptId'],
            'methods' => [
                'list' => ['operation' => 'ListEdgeScriptSecretsEndpoint_ListEdgeScriptSecrets', 'unwrap' => 'Secrets'],
                // The name identifies the secret; the value is the only thing to set.
                'create' => ['operation' => 'AddEdgeScriptSecretEndpoint_AddEdgeScriptSecret', 'flatten' => true, 'required' => ['Secret']],
                'update' => ['operation' => 'UpdateEdgeScriptSecretEndpoint_UpdateEdgeScriptSecret', 'flatten' => true, 'required' => ['Secret']],
                // bunny.net's Terraform provider expects 204 when updating an existing secret.
                'upsert' => [
                    'operation' => 'UpsertEdgeScriptSecretEndpoint_UpsertEdgeScriptSecret',
                    'flatten' => true,
                    'required' => ['Name', 'Secret'],
                    'note' => 'Returns the secret if it was created, and null if an existing secret was updated (204 No Content).',
                ],
                'delete' => ['operation' => 'DeleteEdgeScriptSecretEndpoint_DeleteSecret'],
            ],
        ],
    ],

    'ignored' => [],
];
