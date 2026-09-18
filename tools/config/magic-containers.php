<?php

declare(strict_types=1);

/**
 * Generator configuration of the Magic Containers API.
 */
return [
    'name' => 'magic-containers',
    'title' => 'Magic Containers API',
    'spec' => 'magic-containers',
    'namespace' => 'MagicContainers',
    'client' => 'MagicContainersClient',
    'baseUri' => 'https://api.bunny.net/mc',
    'credential' => 'apiKey',

    // The specification marks no request body as required, yet none is optional.
    'requestBodiesRequired' => true,

    'schemas' => [
        'AppListItem' => 'ApplicationListItem',
        'ListVolumesResponse' => 'VolumeList',
        'UpdateVolumeResponse' => 'UpdatedVolume',
        'DetachVolumeResponse' => 'DetachedVolume',
        'ProtocolV3' => 'Protocol',
    ],

    'properties' => [],

    'enumCases' => [],

    'extraModels' => [],

    'pagination' => [
        // {"items", "meta": {"totalItems"}, "cursor"}; the limit defaults to 20
        // and accepts 1…1000 (verified live).
        'cursor' => [
            'position' => 'nextCursor',
            'positionType' => 'string',
            'size' => 'limit',
            'pageSize' => 100,
            'allSize' => 1000,
            'items' => 'items',
            'factory' => 'Pagination::cursor',
            'withPosition' => true,
        ],
    ],

    'resources' => [
        'apps' => [
            'class' => 'ApplicationResource',
            'description' => 'Applications: their deployment, settings, statistics and costs.',
            'methods' => [
                'list' => ['operation' => 'ListApplications', 'pagination' => 'cursor', 'all' => 'all'],
                'get' => ['operation' => 'GetApplication'],
                'create' => ['operation' => 'AddApplication', 'unwrap' => 'id', 'parameters' => ['@body' => 'application']],
                'update' => ['operation' => 'UpdateApplication', 'unwrap' => 'id', 'parameters' => ['@body' => 'application']],
                'patch' => ['operation' => 'PatchApplication', 'unwrap' => 'id', 'parameters' => ['@body' => 'changes']],
                'delete' => ['operation' => 'DeleteApplication'],
                'deploy' => ['operation' => 'DeployApplication'],
                'undeploy' => ['operation' => 'UndeployApplication'],
                'restart' => ['operation' => 'RestartApplication'],
                'statistics' => ['operation' => 'GetApplicationStatistics'],
                'summary' => ['operation' => 'GetApplicationUsageSummary'],
                'overview' => ['operation' => 'GetApplicationOverview'],
                'autoscaling' => ['operation' => 'GetApplicationAutoscaling'],
                'setAutoscaling' => ['operation' => 'UpdateApplicationAutoscaling', 'parameters' => ['@body' => 'settings']],
                'regionSettings' => ['operation' => 'GetApplicationRegionSettings'],
                'setRegionSettings' => ['operation' => 'UpdateApplicationRegionSettings', 'parameters' => ['@body' => 'settings']],
            ],
        ],
        'containers' => [
            'class' => 'ContainerResource',
            'description' => 'The container templates of applications.',
            'methods' => [
                'get' => ['operation' => 'GetApplicationContainerTemplate'],
                'create' => ['operation' => 'AddApplicationContainerTemplate', 'parameters' => ['@body' => 'container']],
                'patch' => ['operation' => 'PatchApplicationContainerTemplate', 'parameters' => ['@body' => 'changes']],
                'delete' => ['operation' => 'DeleteApplicationContainerTemplate'],
                'setEnvironmentVariables' => ['operation' => 'SetContainerEnvironmentVariables', 'parameters' => ['@body' => 'variables']],
            ],
        ],
        'endpoints' => [
            'class' => 'EndpointResource',
            'description' => 'The endpoints that expose containers through the CDN or anycast IPs.',
            'methods' => [
                'list' => ['operation' => 'ListApplicationEndpoints', 'unwrap' => 'items'],
                'create' => ['operation' => 'AddApplicationEndpoint', 'unwrap' => 'id', 'parameters' => ['@body' => 'endpoint']],
                'update' => ['operation' => 'UpdateApplicationEndpoint', 'parameters' => ['@body' => 'endpoint']],
                'delete' => ['operation' => 'DeleteApplicationEndpoint'],
            ],
        ],
        'volumes' => [
            'class' => 'VolumeResource',
            'description' => 'The persistent volumes of applications.',
            'methods' => [
                'list' => ['operation' => 'ListVolumes'],
                'update' => ['operation' => 'UpdateVolume', 'parameters' => ['@body' => 'changes']],
                'detach' => ['operation' => 'DetachVolume'],
                'delete' => ['operation' => 'DeleteAllVolumeInstances', 'unwrap' => 'ids'],
                'deleteInstance' => ['operation' => 'DeleteVolumeInstance', 'unwrap' => 'id'],
            ],
        ],
        'pods' => [
            'class' => 'PodResource',
            'description' => 'The running instances of applications.',
            'methods' => [
                'recreate' => ['operation' => 'RecreatePod'],
            ],
        ],
        'registries' => [
            'class' => 'RegistryResource',
            'description' => 'Container registries and the images they hold.',
            'methods' => [
                'list' => ['operation' => 'ListContainerRegistries', 'unwrap' => 'items'],
                // Verified live: the global registries that list() includes answer 404.
                'get' => [
                    'operation' => 'GetContainerRegistry',
                    'note' => 'Only the registries of the account; the global public registries that list() includes answer 404.',
                ],
                'create' => ['operation' => 'AddContainerRegistry', 'parameters' => ['@body' => 'registry']],
                'update' => ['operation' => 'UpdateContainerRegistry', 'parameters' => ['@body' => 'registry']],
                'delete' => ['operation' => 'DeleteContainerRegistry'],
                'images' => ['operation' => 'ListContainerImages', 'flatten' => true],
                'searchPublicImages' => ['operation' => 'SearchForPublicContainerImages', 'flatten' => true],
                'tags' => ['operation' => 'ListContainerImageTags', 'flatten' => true],
                'imageConfig' => ['operation' => 'GetImageConfig', 'flatten' => true],
                'digest' => ['operation' => 'GetContainerImageTagDigest', 'flatten' => true],
                'configSuggestions' => ['operation' => 'GetContainerConfigSuggestions', 'flatten' => true],
            ],
        ],
        'regions' => [
            'class' => 'RegionResource',
            'description' => 'The regions applications can run in.',
            'methods' => [
                'list' => ['operation' => 'ListRegions', 'pagination' => 'cursor', 'all' => 'all'],
                'optimal' => ['operation' => 'GetOptimalBaseRegion', 'unwrap' => 'region'],
            ],
        ],
        'nodes' => [
            'class' => 'NodeResource',
            'description' => 'The IP addresses of the Magic Containers nodes, e.g. for allowlists.',
            'methods' => [
                'list' => ['operation' => 'ListNodes', 'pagination' => 'cursor', 'all' => 'all'],
                'plain' => ['operation' => 'ListNodesPlain'],
            ],
        ],
        'limits' => [
            'class' => 'LimitResource',
            'description' => 'The limits of the account.',
            'methods' => [
                'get' => ['operation' => 'GetLimits'],
            ],
        ],
        'logForwarding' => [
            'class' => 'LogForwardingResource',
            'description' => 'Forwarding of application logs to syslog endpoints.',
            'methods' => [
                // A plain array, not {"items"} (verified live).
                'list' => ['operation' => 'ListLogForwardingConfigurations', 'response' => 'list:LogForwardingConfiguration'],
                'get' => ['operation' => 'GetLogForwardingConfiguration'],
                'create' => ['operation' => 'CreateLogForwardingConfiguration', 'parameters' => ['@body' => 'configuration']],
                'update' => ['operation' => 'UpdateLogForwardingConfiguration', 'parameters' => ['@body' => 'configuration']],
                'delete' => ['operation' => 'DeleteLogForwardingConfiguration'],
            ],
        ],
    ],

    'ignored' => [],
];
