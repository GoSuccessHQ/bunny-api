<?php

declare(strict_types=1);

/**
 * Generator configuration of the Core Platform API.
 *
 * Every deviation from the specification is documented next to it together
 * with the evidence it is based on.
 */
return [
    'name' => 'core',
    'title' => 'Core Platform API',
    'spec' => 'core',
    'namespace' => 'Core',
    'client' => 'CoreClient',
    'baseUri' => 'https://api.bunny.net',
    'credential' => 'apiKey',

    'schemas' => [
        // Response models
        'BillingAffiliateDetailsModel' => 'AffiliateDetails',
        'BillingModel' => 'BillingDetails',
        'CloseAccountResponseModel' => 'CloseAccountResult',
        // The specification misspells "Environmental"; only the class name is corrected.
        'DnsRecordEnviromentalVariableModel' => 'DnsRecordEnvironmentVariable',
        'DnsZoneDiscoveredRecordModel' => 'DiscoveredDnsRecord',
        'DnsZoneRecordScanJobResponse' => 'DnsRecordScan',
        'DnsZoneRecordScanTriggerResponse' => 'DnsRecordScanTrigger',
        'EdgeRuleV2ActionModel' => 'EdgeRuleAction',
        'EdgeRuleV2Model' => 'EdgeRule',
        'SearchResultItemModel' => 'SearchResult',
        'Trigger' => 'EdgeRuleTrigger',
        'UserAuditLog' => 'AuditLogEntry',

        // Request models
        'AddDnsRecordModel' => 'DnsRecordCreate',
        'PullZoneAddModel' => 'PullZoneCreate',
        'PullZoneOptimizerClassModel' => 'OptimizerClassInput',
        'PullZoneSettingsModel' => 'PullZoneUpdate',
        'StorageZoneModelAdd' => 'StorageZoneCreate',
        'StorageZoneSettingsModel' => 'StorageZoneUpdate',
        'UpdateDnsRecordModel' => 'DnsRecordUpdate',
        'UpdateDnsZoneModel' => 'DnsZoneUpdate',

        // Enums
        'DnsRecordTypes' => 'DnsRecordType',
        // Identical to DnsRecordTypes; the generator verifies that.
        'DnsRecordTypes2' => 'DnsRecordType',
        'DnsZoneScanJobStatus' => 'DnsRecordScanStatus',
        'PatternMatchingTypes' => 'PatternMatchingType',
        'TriggerMatchingTypes' => 'TriggerMatchingType',
        'TriggerTypes' => 'EdgeRuleTriggerType',
    ],

    'properties' => [],

    'enumCases' => [],

    'extraModels' => [],

    'pagination' => [
        // Items, CurrentPage, TotalItems, HasMoreItems. The API silently raises a
        // perPage below 5 to 5 (verified live), so page sizes stay well above it.
        'page' => [
            'position' => 'page',
            'positionType' => 'int',
            'first' => 1,
            'size' => 'perPage',
            'pageSize' => 100,
            'allSize' => 1000,
            'items' => 'Items',
            'factory' => 'Pagination::page',
        ],
        // Logs, HasMoreData, ContinuationToken (an empty string on the last page).
        'continuation' => [
            'position' => 'ContinuationToken',
            'positionType' => 'string',
            'size' => 'Limit',
            'items' => 'Logs',
            'factory' => 'Pagination::continuation',
        ],
    ],

    'resources' => [
        'pullZones' => [
            'class' => 'PullZoneResource',
            'description' => 'Pull zones: CDN configuration, hostnames, certificates, referrer and IP blocking, cache purging and statistics.',
            'methods' => [
                'list' => [
                    'operation' => 'PullZonePublic_IndexAll',
                    'pagination' => 'page',
                    'all' => 'all',
                    // The specification documents the legacy plain array returned without
                    // a page. With page >= 1 the API returns the usual page envelope
                    // (verified live), which is what this client always requests.
                    'response' => 'PullZoneModel',
                ],
                'count' => ['operation' => 'PullZonePublic_Count', 'unwrap' => 'Count'],
                'get' => ['operation' => 'PullZonePublic_Index'],
                'create' => [
                    'operation' => 'PullZonePublic_Add',
                    'parameters' => ['@body' => 'pullZone'],
                    // The specification documents 201 without a body, but the API returns the
                    // created pull zone: bunny.net's own Terraform provider decodes it
                    // (BunnyWay/terraform-provider-bunnynet, internal/api/pullzone.go, CreatePullzone).
                    'response' => 'PullZoneModel',
                ],
                'update' => ['operation' => 'PullZonePublic_UpdatePullZone', 'parameters' => ['@body' => 'changes']],
                'delete' => ['operation' => 'PullZonePublic_Delete'],
                'addHostname' => ['operation' => 'PullZonePublic_AddHostname', 'flatten' => true],
                'removeHostname' => ['operation' => 'PullZonePublic_RemoveHostname', 'flatten' => true],
                'setForceSsl' => ['operation' => 'PullZonePublic_SetForceSSL', 'flatten' => true],
                'addCertificate' => ['operation' => 'PullZonePublic_AddCertificate', 'flatten' => true],
                'removeCertificate' => ['operation' => 'PullZonePublic_RemoveCertificate', 'flatten' => true],
                'loadFreeCertificate' => [
                    'operation' => 'PullZonePublic_LoadFreeCertificate',
                    'note' => 'Despite being a GET request, this issues a certificate and therefore changes state.',
                ],
                'updatePrivateKeyType' => ['operation' => 'PullZonePublic_UpdatePrivateKeyType', 'flatten' => true],
                'requestExternalDnsCertificate' => ['operation' => 'PullZonePublic_RequestExternalDnsCertificate', 'flatten' => true],
                'completeExternalDnsCertificate' => ['operation' => 'PullZonePublic_CompleteExternalDnsCertificate', 'flatten' => true],
                'requestExternalHttpCertificate' => ['operation' => 'PullZonePublic_RequestExternalHttpCertificate', 'flatten' => true],
                'completeExternalHttpCertificate' => ['operation' => 'PullZonePublic_CompleteExternalHttpCertificate', 'flatten' => true],
                'purgeCache' => ['operation' => 'PullZonePublic_PurgeCachePostByTag', 'flatten' => true],
                'checkAvailability' => ['operation' => 'PullZonePublic_CheckAvailability', 'handwritten' => true],
                'resetSecurityKey' => ['operation' => 'ResetSecurityKeyEndpoint_ResetSecurityKey', 'flatten' => true],
                'addAllowedReferrer' => ['operation' => 'PullZonePublic_AddAllowedReferrer', 'flatten' => true],
                'removeAllowedReferrer' => ['operation' => 'PullZonePublic_RemoveAllowedReferrer', 'flatten' => true],
                'addBlockedReferrer' => ['operation' => 'PullZonePublic_AddBlockedReferrer', 'flatten' => true],
                'removeBlockedReferrer' => ['operation' => 'PullZonePublic_RemoveBlockedReferrer', 'flatten' => true],
                'addBlockedIp' => ['operation' => 'PullZonePublic_AddBlockedIp', 'flatten' => true],
                'removeBlockedIp' => ['operation' => 'PullZonePublic_RemoveBlockedIp', 'flatten' => true],
                'optimizerStatistics' => ['operation' => 'GetOptimizerStatisticsEndpoint_GetOptimizerStatistics'],
                'originShieldQueueStatistics' => ['operation' => 'GetOriginShieldConcurrencyStatisticsEndpoint_GetOriginShieldConcurrencyStatistics'],
                'safeHopStatistics' => ['operation' => 'GetSafeHopStatisticsEndpoint_GetSafeHopStatistics'],
            ],
            'handwritten' => true,
        ],
        'edgeRules' => [
            'class' => 'EdgeRuleResource',
            'description' => 'Edge rules of pull zones.',
            'handwritten' => true,
            'methods' => [
                'addOrUpdate' => [
                    'operation' => 'PullZonePublic_AddEdgeRule',
                    'parameters' => ['@body' => 'edgeRule'],
                    // The specification documents 201 without a body, but the API returns the
                    // saved rule including the Guid of a new one: bunny.net's Terraform
                    // provider decodes it (internal/api/pullzone_edgerule.go, CreatePullzoneEdgerule).
                    'response' => 'EdgeRuleV2Model',
                ],
                'delete' => ['operation' => 'PullZonePublic_DeleteEdgeRule'],
                // The body's "Id" must be the pull zone id, which the specification does not
                // say; the method fills it in from the path.
                'setEnabled' => ['operation' => 'PullZonePublic_SetEdgeRuleEnabled', 'handwritten' => true],
            ],
        ],
        'purge' => [
            'class' => 'PurgeResource',
            'description' => 'Purging single URLs from the CDN cache.',
            'methods' => [
                'url' => ['operation' => 'PurgePublic_IndexPost'],
            ],
        ],
        'storageZones' => [
            'class' => 'StorageZoneResource',
            'description' => 'Storage zones: management of Edge Storage zones. Files are handled by the Storage API.',
            'handwritten' => true,
            'methods' => [
                'list' => [
                    'operation' => 'StorageZonePublic_IndexAll',
                    'pagination' => 'page',
                    'all' => 'all',
                    // Like pull zones: the page envelope is returned for page >= 1 (verified live).
                    'response' => 'StorageZoneModel',
                ],
                'get' => ['operation' => 'StorageZonePublic_Index'],
                'create' => ['operation' => 'StorageZonePublic_Add', 'parameters' => ['@body' => 'storageZone']],
                'update' => ['operation' => 'StorageZonePublic_Update', 'parameters' => ['@body' => 'changes']],
                'delete' => ['operation' => 'StorageZonePublic_Delete'],
                'checkAvailability' => ['operation' => 'StorageZonePublic_CheckAvailability', 'handwritten' => true],
                'resetPassword' => ['operation' => 'StorageZonePublic_ResetPassword'],
                'resetReadOnlyPassword' => ['operation' => 'StorageZonePublic_ResetReadOnlyPassword'],
                'statistics' => ['operation' => 'GetStoragezoneStatisticsEndpoint_StorageZoneStatistics'],
                'egressStatistics' => ['operation' => 'GetStoragezoneEgressStatisticsEndpoint_StorageZoneEgressStatistics'],
                'regions' => ['operation' => 'GetStoragezoneRegionsEndpoint_GetStorageZoneRegions'],
            ],
        ],
        'dnsZones' => [
            'class' => 'DnsZoneResource',
            'description' => 'DNS zones: zones, DNSSEC, zone file import/export and statistics.',
            'handwritten' => true,
            'methods' => [
                'list' => ['operation' => 'DnsZonePublic_Index', 'pagination' => 'page', 'all' => 'all'],
                'get' => ['operation' => 'DnsZonePublic_Index2'],
                'create' => [
                    'operation' => 'DnsZonePublic_Add',
                    'flatten' => true,
                    // The specification documents 201 without a body, but the API returns the
                    // created zone: bunny.net's Terraform provider decodes it
                    // (internal/api/dnszone.go, CreateDnsZone).
                    'response' => 'DnsZoneModel',
                ],
                'update' => ['operation' => 'DnsZonePublic_Update', 'parameters' => ['@body' => 'changes']],
                'delete' => ['operation' => 'DnsZonePublic_Delete'],
                'export' => ['operation' => 'DnsZonePublic_Export', 'handwritten' => true],
                'import' => ['operation' => 'DnsZonePublic_Import', 'handwritten' => true],
                'checkAvailability' => ['operation' => 'DnsZonePublic_CheckAvailability', 'handwritten' => true],
                'issueWildcardCertificate' => ['operation' => 'DnsZonePublic_IssueWildcardCertificate', 'flatten' => true],
                'statistics' => ['operation' => 'GetDnsZoneStatisticsEndpoint_Statistics'],
                'enableDnsSec' => ['operation' => 'ManageDnsZoneDnsSecEndpoint_EnableDnsSecDnsZone'],
                'disableDnsSec' => ['operation' => 'ManageDnsZoneDnsSecEndpoint_DisableDnsSecDnsZone'],
                'scanRecords' => ['operation' => 'TriggerDnsZoneRecordScanEndpoint_TriggerScan', 'flatten' => true],
                'latestRecordScan' => ['operation' => 'TriggerDnsZoneRecordScanEndpoint_GetLatestScan'],
            ],
        ],
        'dnsRecords' => [
            'class' => 'DnsRecordResource',
            'description' => 'DNS records of a zone.',
            'methods' => [
                'list' => ['operation' => 'DnsZonePublic_ListDnsZoneRecords', 'pagination' => 'page', 'all' => 'all'],
                'create' => ['operation' => 'DnsZonePublic_AddRecord', 'parameters' => ['@body' => 'record']],
                'update' => ['operation' => 'DnsZonePublic_UpdateRecord', 'parameters' => ['@body' => 'changes']],
                'delete' => ['operation' => 'DnsZonePublic_DeleteRecord'],
            ],
        ],
        'videoLibraries' => [
            'class' => 'VideoLibraryResource',
            'description' => 'Stream video libraries: settings, API keys, referrer rules, watermarks and statistics. Videos are handled by the Stream API.',
            'handwritten' => true,
            'methods' => [
                'list' => ['operation' => 'ListVideoLibrariesEndpoint_ListVideoLibraries', 'pagination' => 'page', 'all' => 'all'],
                'get' => ['operation' => 'GetVideoLibraryEndpoint_GetVideoLibrary'],
                'create' => ['operation' => 'AddVideoLibraryEndpoint_AddVideoLibrary', 'parameters' => ['@body' => 'library']],
                'update' => ['operation' => 'UpdateVideoLibraryEndpoint_UpdateVideoLibrary', 'parameters' => ['@body' => 'changes']],
                'delete' => ['operation' => 'DeleteVideoLibraryEndpoint_DeleteVideoLibrary'],
                'languages' => ['operation' => 'GetVideoLibraryLanguagesEndpoint_GetVideoLibraryLanguages'],
                'resetApiKey' => ['operation' => 'ResetVideoLibraryApiKeyEndpoint_ResetVideoLibraryApiKey'],
                'resetReadOnlyApiKey' => ['operation' => 'ResetVideoLibraryReadOnlyApiKeyEndpoint_ResetVideoLibraryReadOnlyApiKey'],
                'addAllowedReferrer' => ['operation' => 'AddAllowedReferrerEndpoint_AddAllowedReferrer', 'flatten' => true],
                'removeAllowedReferrer' => ['operation' => 'RemoveAllowedReferrerEndpoint_RemoveAllowedReferrer', 'flatten' => true],
                'addBlockedReferrer' => ['operation' => 'AddBlockedReferrerEndpoint_AddBlockedReferrer', 'flatten' => true],
                'removeBlockedReferrer' => ['operation' => 'RemoveBlockedReferrerEndpoint_RemoveBlockedReferrer', 'flatten' => true],
                'addWatermark' => ['operation' => 'AddWatermarkEndpoint_AddWatermark', 'handwritten' => true],
                'deleteWatermark' => ['operation' => 'DeleteWatermarkEndpoint_DeleteWatermark'],
                'addLiveThumbnail' => ['operation' => 'AddLiveThumbnailEndpoint_AddThumbnail', 'handwritten' => true],
                'deleteLiveThumbnail' => ['operation' => 'DeleteLiveThumbnailEndpoint_DeleteThumbnail'],
                'addLiveWatermark' => ['operation' => 'AddLiveWatermarkEndpoint_AddWatermark', 'handwritten' => true],
                'deleteLiveWatermark' => ['operation' => 'DeleteLiveWatermarkEndpoint_DeleteWatermark'],
                'transcribingStatistics' => ['operation' => 'GetTranscribingStatisticsEndpoint_Statistics'],
                'drmStatistics' => ['operation' => 'GetDrmStatisticsEndpoint_Statistics'],
            ],
        ],
        'statistics' => [
            'class' => 'StatisticsResource',
            'description' => 'Account-wide and per-zone traffic statistics.',
            'methods' => [
                'get' => ['operation' => 'StatisticsPublic_GetStatistics'],
            ],
        ],
        'loadBalancers' => [
            'class' => 'LoadBalancerResource',
            'description' => 'Load balancer statistics and usage.',
            'methods' => [
                'statistics' => ['operation' => 'LoadBalancerStatisticsEndpoint_GetLoadBalancerStatistics'],
                'attachedPullZones' => ['operation' => 'LoadBalancerStatisticsEndpoint_GetAttachedPullZones'],
                'accountStatistics' => ['operation' => 'LoadBalancerStatisticsEndpoint_GetLoadBalancerAccountStatistics'],
                'usage' => ['operation' => 'LoadBalancerUsageEndpoint_GetLoadBalancerUsage'],
            ],
        ],
        'billing' => [
            'class' => 'BillingResource',
            'description' => 'Billing details, summaries, payment requests and invoices.',
            'handwritten' => true,
            'methods' => [
                'details' => ['operation' => 'GetBillingDetailsEndpoint_GetBillingDetails'],
                'summary' => ['operation' => 'GetBillingSummaryEndpoint_GetSummaryEndpoint'],
                'summaryPdf' => ['operation' => 'BillingSummaryPublic_GetBillingSummaryPdf', 'handwritten' => true],
                'paymentRequests' => ['operation' => 'GetPaymentRequestsEndpoint_GetPaymentRequests'],
                'paymentRequestInvoicePdf' => ['operation' => 'DownloadPaymentRequestInvoicePdfEndpoint_DownloadPaymentRequestInvoicePdf', 'handwritten' => true],
                'affiliate' => ['operation' => 'GetAffiliateDetailsEndpoint_AffiliateDetails'],
            ],
        ],
        'apiKeys' => [
            'class' => 'ApiKeyResource',
            'description' => 'API keys of the account.',
            'methods' => [
                'list' => ['operation' => 'ApiKeyPublic_GetApiKeysByAccountEndpoint', 'pagination' => 'page', 'all' => 'all'],
            ],
        ],
        'auditLog' => [
            'class' => 'AuditLogResource',
            'description' => 'The audit log of changes made to the account.',
            'methods' => [
                'list' => ['operation' => 'GetUserAuditLogEndpoint_GetUserAuditLog', 'pagination' => 'continuation', 'all' => 'all'],
            ],
        ],
        'account' => [
            'class' => 'AccountResource',
            'description' => 'Account-level operations.',
            'methods' => [
                'close' => [
                    'operation' => 'CloseAccountEndpoint_CloseAccount',
                    'flatten' => true,
                    'note' => 'Irreversible: this closes the whole bunny.net account.',
                ],
            ],
        ],
        'countries' => [
            'class' => 'CountryResource',
            'description' => 'The countries known to bunny.net, e.g. for geo blocking.',
            'methods' => [
                'list' => ['operation' => 'CountriesPublic_GetCountryList'],
            ],
        ],
        'regions' => [
            'class' => 'RegionResource',
            'description' => 'The CDN regions and their pricing.',
            'methods' => [
                'list' => ['operation' => 'RegionPublic_Index'],
            ],
        ],
        'search' => [
            'class' => 'SearchResource',
            'description' => 'Global search across the resources of the account.',
            'methods' => [
                'query' => ['operation' => 'SearchPublic_GlobalSearchEndpoint'],
            ],
        ],
        'pricing' => [
            'class' => 'PricingResource',
            'description' => 'Price estimations.',
            'methods' => [
                'estimate' => ['operation' => 'GetPriceEstimationEndpoint_GetPriceEstimation'],
            ],
        ],
    ],

    'ignored' => [],
];
