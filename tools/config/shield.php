<?php

declare(strict_types=1);

/**
 * Generator configuration of the Shield API.
 */
return [
    'name' => 'shield',
    'title' => 'Shield API',
    'spec' => 'shield',
    'namespace' => 'Shield',
    'client' => 'ShieldClient',
    'baseUri' => 'https://api.bunny.net',
    'credential' => 'apiKey',

    // Payloads arrive as {"data": …, "error": …}; lists add "page". Many
    // failures, "not found" among them, come as 202 with the error in "error"
    // or "errorResponse" (verified live); Envelope turns them into exceptions.
    'envelope' => 'data',
    'errorProperties' => ['error', 'errorResponse'],
    'errorStatus' => 'Envelope::errorStatus',

    // {"statusCode", "success", "message", "errorKey"} carries no payload.
    'voidResponses' => ['GenericRequestResponse'],

    'schemas' => [
        'ShieldZoneResponse' => 'ShieldZone',
        'ShieldZoneRequest' => 'ShieldZoneSettings',
        'CreateShieldZoneRequest' => 'ShieldZoneSetup',
        'UpdateShieldZoneRequest' => 'ShieldZoneUpdate',
        'AccessListsDetailsResponse' => 'AccessListOverview',
        'UpdateAccessListConfigurationRequest' => 'AccessListConfiguration',
        'BotCategorizationDetails' => 'BotCategorization',
        'BotCategoryDetails' => 'BotCategorySetting',
        'BotDetectionConfigurationState' => 'BotDetectionConfiguration',
        'UploadScanningConfigurationState' => 'UploadScanningConfiguration',
        'ApiGuardianConfigurationResponse' => 'ApiGuardian',
        'ApiGuardianConfigurationDetails' => 'ApiGuardianConfiguration',
        'ApiGuardianEndpointDetails' => 'ApiGuardianEndpoint',
        'GetTriggeredRulesResponse' => 'TriggeredRules',
        'TriggeredRuleItem' => 'TriggeredRule',
        'TriggeredRuleRecommendationResponse' => 'TriggeredRuleRecommendation',
        'ShieldZoneMonthlyOveragesResult' => 'MonthlyOverages',
        'WafLoggingResponse' => 'EventLogPage',
        'Log' => 'EventLog',
        'Labels' => 'EventLogLabels',
        'EventLogsSearchResponse' => 'EventLogSearchResult',
        'EventRow' => 'EventLogRow',
        'EventGroup' => 'EventLogGroup',
        'CustomWafRule' => 'CustomRule',
        'CreateCustomWafRuleModel' => 'CustomRuleConfiguration',
        'CreateWafRateLimitRuleModel' => 'RateLimitRuleConfiguration',
        'WafChainedRuleConditionItem' => 'ChainedRuleCondition',
        'WafMappedEnumList' => 'WafEnum',
        'WafMappedEnum' => 'WafEnumValue',
        'WafProfileMinimal' => 'WafProfile',
        'ConfigVariableValueMinimal' => 'WafEngineSetting',
        'PullZoneWafConfigVariableModel' => 'ZoneWafEngineSetting',
        // Metrics: counters are "…Counts", responses "…Metrics".
        'ShieldZoneMetrics' => 'ZoneMetrics',
        'ShieldOverview' => 'OverviewCounts',
        'ShieldOverviewMetricsData' => 'DetailedMetrics',
        'OverviewMetric' => 'MetricSeries',
        'WafRule' => 'WafRuleCounts',
        'Waf' => 'WafCounts',
        'DDoS' => 'DdosCounts',
        'Ratelimit' => 'RateLimitCounts',
        'IndividualRatelimit' => 'RateLimitRuleCounts',
        'ShieldZoneRatelimit' => 'ZoneRateLimitCounts',
        'RatelimitMetrics' => 'RateLimitRuleMetrics',
        'ShieldZoneRatelimitMetrics' => 'ZoneRateLimitMetrics',
        'BotDetectionData' => 'BotDetectionCounts',
        'AccessList' => 'AccessListCounts',
        'UploadScanning' => 'UploadScanningCounts',
        'ApiGuardianEndpointActivity' => 'ApiGuardianCounts',
        'ShieldZoneBotDetectionMetrics' => 'BotDetectionMetrics',
        'ShieldZoneUploadScanningMetrics' => 'UploadScanningMetrics',
        // Three copies of one inline object: parameter names by location.
        'ApiGuardianEndpointDetails.availableParameters' => 'ApiGuardianParameters',
        'ApiGuardianEndpointDetails.injectionDetectionParameters' => 'ApiGuardianParameters',
        'UpdateApiGuardianEndpointRequest.injectionDetectionParameters' => 'ApiGuardianParameters',
        // Enums, with acronyms written like the other class names.
        'DDoSExecutionMode' => 'DdosExecutionMode',
        'DDoSShieldSensitivity' => 'DdosSensitivity',
        'WAFExecutionMode' => 'WafExecutionMode',
        'WAFPayloadLimitAction' => 'WafPayloadLimitAction',
        'RatelimitRuleActionType' => 'RateLimitAction',
        'WafRatelimitBlockType' => 'RateLimitBlockTime',
        'WafRatelimitCounterKeyType' => 'RateLimitCounterKey',
        'WafRateLimitTimeframeType' => 'RateLimitTimeframe',
    ],

    'properties' => [],

    'enumCases' => [],

    // Returned by the hand-written eventLogs->list() and eventLogs->search().
    'extraModels' => ['Log', 'EventLogsSearchResponse'],

    // A closed object with one optional string per WAF variable; bunny.net's
    // Terraform provider sends it as a map, too.
    'stringMaps' => [
        'CreateCustomWafRuleModel.variableTypes',
        'CreateWafRateLimitRuleModel.variableTypes',
        'WafChainedRuleConditionItem.variableTypes',
    ],

    'pagination' => [
        // {"data", "page": {"totalCount", "totalPages", "currentPage", "nextPage",
        // "pageSize"}}; perPage accepts 1…1000 (verified live).
        'page' => [
            'position' => 'page',
            'positionType' => 'int',
            'first' => 1,
            'size' => 'perPage',
            'pageSize' => 100,
            'allSize' => 1000,
            'items' => 'data',
            'factory' => 'Pagination::page',
        ],
    ],

    'resources' => [
        'zones' => [
            'class' => 'ShieldZoneResource',
            'description' => 'Shield zones: the protection settings of pull zones.',
            'methods' => [
                'list' => ['operation' => 'Get Shield Zone Configurations', 'pagination' => 'page', 'all' => 'all'],
                'get' => ['operation' => 'Get Shield Zone Configuration'],
                'getByPullZone' => ['operation' => 'Get Shield Zone Configuration by Pull Zone'],
                'pullZoneMapping' => ['operation' => 'Get Shield Zones Pull Zone Mapping'],
                'defaults' => ['operation' => 'Get Shield Zone Defaults'],
                'create' => ['operation' => 'Create Shield Zone', 'flatten' => true],
                'createUnderAttack' => ['operation' => 'Create Shield Zone Under Attack', 'flatten' => true],
                'update' => ['operation' => 'Update Shield Zone', 'flatten' => true],
            ],
        ],
        'waf' => [
            'class' => 'WafResource',
            'description' => 'The managed WAF rules, their review and the WAF settings catalog.',
            'methods' => [
                'rules' => ['operation' => 'Get WAF Rules'],
                'rulesByPlan' => ['operation' => 'Get WAF Rules by Plan'],
                'triggeredRules' => ['operation' => 'Get Triggered WAF Rules'],
                // {"success"} only; failures are errors. A review needs both the rule and the action.
                'reviewTriggeredRule' => ['operation' => 'Update Triggered WAF Rule', 'flatten' => true, 'response' => 'void', 'required' => ['ruleId', 'action']],
                'recommendation' => ['operation' => 'Get Triggered Rule AI Recommendation'],
                'profiles' => ['operation' => 'List WAF Profiles'],
                'enums' => ['operation' => 'Get WAF Enum Mappings'],
                'engineConfig' => ['operation' => 'Get WAF Engine Configuration'],
            ],
        ],
        'customRules' => [
            'class' => 'CustomRuleResource',
            'description' => 'Custom WAF rules of Shield zones.',
            'methods' => [
                'list' => ['operation' => 'Get Custom WAF Rules', 'pagination' => 'page', 'all' => 'all'],
                'get' => ['operation' => 'Get Custom WAF Rule'],
                'create' => ['operation' => 'Create Custom WAF Rule', 'flatten' => true, 'required' => ['shieldZoneId']],
                'update' => ['operation' => 'PATCH /shield/waf/custom-rule/{id}', 'flatten' => true],
                'replace' => ['operation' => 'PUT /shield/waf/custom-rule/{id}', 'flatten' => true],
                // 200 carries a bare status; 202 is an ASP.NET AcceptedResult artifact.
                'delete' => ['operation' => 'Delete Custom WAF Rule', 'response' => 'void'],
            ],
        ],
        'rateLimits' => [
            'class' => 'RateLimitResource',
            'description' => 'Rate limit rules of Shield zones.',
            'methods' => [
                // The specification reuses the custom WAF rule schema; the API
                // returns rate limit rules (verified live).
                'list' => ['operation' => 'Get Rate Limits', 'pagination' => 'page', 'all' => 'all', 'response' => 'RateLimitRule'],
                'get' => ['operation' => 'Get Rate Limit', 'response' => 'RateLimitRule'],
                'create' => ['operation' => 'Create Rate Limit', 'flatten' => true, 'required' => ['shieldZoneId']],
                'update' => ['operation' => 'Update Rate Limit', 'flatten' => true],
                'delete' => ['operation' => 'Delete Rate Limit'],
            ],
        ],
        'accessLists' => [
            'class' => 'AccessListResource',
            'description' => 'Managed and custom access lists of Shield zones.',
            'methods' => [
                'list' => ['operation' => 'Get Access Lists'],
                'get' => ['operation' => 'Get Custom Access List'],
                // Observed live by the gosuccess-control session: a new list starts
                // disabled with the action Log. bunny.net's Terraform provider
                // therefore configures every list right after creating it, looking
                // up its configuration ID in the list of all access lists.
                'create' => [
                    'operation' => 'Create Custom Access List',
                    'flatten' => true,
                    'note' => 'A new list starts disabled, with the action Log. Enable it and choose its action with configure(), which takes the configurationId that list() reports for the list, not the ID returned here.',
                ],
                'update' => ['operation' => 'Update Custom Access List', 'flatten' => true],
                'delete' => ['operation' => 'Delete Custom Access List'],
                'configure' => [
                    'operation' => 'Update Access List Configuration',
                    'flatten' => true,
                    'note' => 'Takes the configuration ID that list() reports as configurationId for every managed and custom list; it differs from the list ID that create() and get() return.',
                ],
                // The zone ID is an integer everywhere else.
                'enums' => ['operation' => 'Get Access List Enums', 'parameterTypes' => ['shieldZoneId' => 'int']],
            ],
        ],
        'botDetection' => [
            'class' => 'BotDetectionResource',
            'description' => 'Bot detection and the handling of known bots and bot categories.',
            'methods' => [
                'get' => ['operation' => 'Get Current Bot Detection'],
                'update' => ['operation' => 'Update Bot Detection', 'flatten' => true],
                'categorization' => ['operation' => 'List Bot Categorizations', 'unwrap' => 'categories'],
                'setBotAction' => ['operation' => 'Set Bot Categorization Action', 'flatten' => true],
                'setCategoryAction' => ['operation' => 'Set Bot Category Action', 'flatten' => true],
            ],
        ],
        'uploadScanning' => [
            'class' => 'UploadScanningResource',
            'description' => 'Antivirus and CSAM scanning of uploads.',
            'methods' => [
                'get' => ['operation' => 'Get Upload Scanning'],
                'update' => ['operation' => 'Update Upload Scanning', 'flatten' => true],
            ],
        ],
        'apiGuardian' => [
            'class' => 'ApiGuardianResource',
            'description' => 'API Guardian: request validation against an OpenAPI specification.',
            'methods' => [
                'get' => ['operation' => 'Get API Guardian'],
                'update' => ['operation' => 'Update API Guardian Configuration', 'flatten' => true],
                'updateEndpoint' => ['operation' => 'Update API Guardian Endpoint', 'flatten' => true],
                'uploadSpecification' => ['operation' => 'Upload OpenAPI Specification', 'flatten' => true],
                'updateSpecification' => ['operation' => 'Update OpenAPI Specification', 'flatten' => true],
                'enums' => ['operation' => 'Get API Guardian Enums', 'parameterTypes' => ['shieldZoneId' => 'int']],
            ],
        ],
        'customPages' => [
            'class' => 'CustomPageResource',
            'description' => 'Custom HTML pages shown to blocked, challenged or rate-limited visitors.',
            'handwritten' => true,
            'methods' => [
                // Raw HTML in both directions, as bunny.net's Terraform provider sends and reads it.
                'get' => ['operation' => 'Get Custom Response Page', 'handwritten' => true],
                'upload' => ['operation' => 'Upload Custom Response Page', 'handwritten' => true],
                'delete' => ['operation' => 'Delete Custom Response Page', 'handwritten' => true],
            ],
        ],
        'eventLogs' => [
            'class' => 'EventLogResource',
            'description' => 'Event logs of the requests Shield acted on.',
            'handwritten' => true,
            'methods' => [
                // The continuation token is a path segment the first page goes without.
                'list' => ['operation' => 'Get Event Logs', 'handwritten' => true],
                'all' => ['operation' => 'Get Event Logs', 'handwritten' => true],
                // Dates instead of Unix milliseconds; the export is CSV.
                'search' => ['operation' => 'Search Event Logs', 'handwritten' => true],
                'export' => ['operation' => 'Export Event Logs', 'handwritten' => true],
            ],
        ],
        'metrics' => [
            'class' => 'MetricsResource',
            'description' => 'Request metrics and billing overages of Shield zones.',
            'methods' => [
                'overview' => ['operation' => 'Get Shield Zone Metrics Overview'],
                'detailed' => ['operation' => 'Get Shield Zone Detailed Metrics Overview'],
                // Verified live: without year and month the API answers 400, and for a
                // month without billing data of the zone 404.
                'overages' => [
                    'operation' => 'Get Shield Zone Monthly Overages',
                    'required' => ['year', 'month'],
                    'note' => 'The API answers 404 for a month without billing data of the zone, e.g. before the zone existed.',
                ],
                'rateLimits' => ['operation' => 'Get Shield Zone Rate Limit Metrics'],
                'rateLimit' => ['operation' => 'Get Rate Limit Metrics'],
                'wafRule' => ['operation' => 'Get WAF Rule Metrics'],
                'botDetection' => ['operation' => 'Get Shield Zone Bot Detection Metrics'],
                'uploadScanning' => ['operation' => 'Get Shield Zone Upload Scanning Metrics'],
                'apiGuardian' => ['operation' => 'Get Shield Zone API Guardian Metrics'],
                'apiGuardianEndpoint' => ['operation' => 'Get API Guardian Endpoint Metrics'],
            ],
        ],
        'ddos' => [
            'class' => 'DdosResource',
            'description' => 'The DDoS settings catalog.',
            'methods' => [
                'enums' => ['operation' => 'Get DDoS Enums'],
            ],
        ],
        'promotions' => [
            'class' => 'PromotionResource',
            'description' => 'Shield promotions of the account.',
            'handwritten' => true,
            'methods' => [
                // The specification documents no response body.
                'state' => ['operation' => 'Get Promotion State', 'handwritten' => true],
            ],
        ],
    ],

    'ignored' => [],
];
