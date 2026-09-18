# API Reference

One page per method. See the [README](../README.md) for an introduction and [examples/](../examples/) for runnable scripts.

## Core Platform API

Client `CoreClient` in `GoSuccess\Bunny\Core`, reached via `$bunny->core`.

### `pullZones`

Pull zones: CDN configuration, hostnames, certificates, referrer and IP blocking, cache purging and statistics.

- [`list()`](core/pullZones/list.md) — List Pull Zones
- [`all()`](core/pullZones/all.md) — Iterate lazily over every item of list(), across all pages
- [`count()`](core/pullZones/count.md) — Count Pull Zones
- [`get()`](core/pullZones/get.md) — Get Pull Zone
- [`create()`](core/pullZones/create.md) — Add Pull Zone
- [`update()`](core/pullZones/update.md) — Update Pull Zone
- [`delete()`](core/pullZones/delete.md) — Delete Pull Zone
- [`addHostname()`](core/pullZones/addHostname.md) — Add Custom Hostname
- [`removeHostname()`](core/pullZones/removeHostname.md) — Remove Custom Hostname
- [`setForceSsl()`](core/pullZones/setForceSsl.md) — Set Force SSL
- [`addCertificate()`](core/pullZones/addCertificate.md) — Add Custom Certificate
- [`removeCertificate()`](core/pullZones/removeCertificate.md) — Remove Certificate
- [`loadFreeCertificate()`](core/pullZones/loadFreeCertificate.md) — Load Free Certificate
- [`updatePrivateKeyType()`](core/pullZones/updatePrivateKeyType.md) — Change hostname private key type
- [`requestExternalDnsCertificate()`](core/pullZones/requestExternalDnsCertificate.md) — Request External DNS Certificate
- [`completeExternalDnsCertificate()`](core/pullZones/completeExternalDnsCertificate.md) — Complete External DNS Certificate
- [`requestExternalHttpCertificate()`](core/pullZones/requestExternalHttpCertificate.md) — Request External HTTP Certificate
- [`completeExternalHttpCertificate()`](core/pullZones/completeExternalHttpCertificate.md) — Complete External HTTP Certificate
- [`purgeCache()`](core/pullZones/purgeCache.md) — Purge Cache
- [`checkAvailability()`](core/pullZones/checkAvailability.md) — Check whether a pull zone name is still available
- [`resetSecurityKey()`](core/pullZones/resetSecurityKey.md) — Reset Token Key
- [`addAllowedReferrer()`](core/pullZones/addAllowedReferrer.md) — Add Allowed Referer
- [`removeAllowedReferrer()`](core/pullZones/removeAllowedReferrer.md) — Remove Allowed Referer
- [`addBlockedReferrer()`](core/pullZones/addBlockedReferrer.md) — Add Blocked Referer
- [`removeBlockedReferrer()`](core/pullZones/removeBlockedReferrer.md) — Remove Blocked Referer
- [`addBlockedIp()`](core/pullZones/addBlockedIp.md) — Add Blocked IP
- [`removeBlockedIp()`](core/pullZones/removeBlockedIp.md) — Remove Blocked IP
- [`optimizerStatistics()`](core/pullZones/optimizerStatistics.md) — Get optimizer statistics
- [`originShieldQueueStatistics()`](core/pullZones/originShieldQueueStatistics.md) — Get Origin Shield Queue Statistics
- [`safeHopStatistics()`](core/pullZones/safeHopStatistics.md) — Get SafeHop Statistics

### `edgeRules`

Edge rules of pull zones.

- [`addOrUpdate()`](core/edgeRules/addOrUpdate.md) — Add/Update Edge Rule
- [`delete()`](core/edgeRules/delete.md) — Delete Edge Rule
- [`setEnabled()`](core/edgeRules/setEnabled.md) — Enable or disable an edge rule

### `purge`

Purging single URLs from the CDN cache.

- [`url()`](core/purge/url.md) — Purge URL

### `storageZones`

Storage zones: management of Edge Storage zones. Files are handled by the Storage API.

- [`list()`](core/storageZones/list.md) — List Storage Zones
- [`all()`](core/storageZones/all.md) — Iterate lazily over every item of list(), across all pages
- [`get()`](core/storageZones/get.md) — Get Storage Zone
- [`create()`](core/storageZones/create.md) — Add Storage Zone
- [`update()`](core/storageZones/update.md) — Update Storage Zone
- [`delete()`](core/storageZones/delete.md) — Delete Storage Zone
- [`checkAvailability()`](core/storageZones/checkAvailability.md) — Check whether a storage zone name is still available
- [`resetPassword()`](core/storageZones/resetPassword.md) — Reset Password
- [`resetReadOnlyPassword()`](core/storageZones/resetReadOnlyPassword.md) — Reset Read-Only Password
- [`statistics()`](core/storageZones/statistics.md) — Get Storage Zone Statistics
- [`egressStatistics()`](core/storageZones/egressStatistics.md) — Get Storage Zone Egress Statistics
- [`regions()`](core/storageZones/regions.md) — Get Storage Zone Regions

### `dnsZones`

DNS zones: zones, DNSSEC, zone file import/export and statistics.

- [`list()`](core/dnsZones/list.md) — List DNS Zones
- [`all()`](core/dnsZones/all.md) — Iterate lazily over every item of list(), across all pages
- [`get()`](core/dnsZones/get.md) — Get DNS Zone
- [`create()`](core/dnsZones/create.md) — Add DNS Zone
- [`update()`](core/dnsZones/update.md) — Update DNS Zones
- [`delete()`](core/dnsZones/delete.md) — Delete DNS Zone
- [`export()`](core/dnsZones/export.md) — Export the records of a zone as a BIND zone file
- [`import()`](core/dnsZones/import.md) — Import records from a BIND zone file
- [`checkAvailability()`](core/dnsZones/checkAvailability.md) — Check whether a zone name is still available
- [`issueWildcardCertificate()`](core/dnsZones/issueWildcardCertificate.md) — Issue new wildcard certificate
- [`statistics()`](core/dnsZones/statistics.md) — Get DNS Query Statistics
- [`enableDnsSec()`](core/dnsZones/enableDnsSec.md) — Enable DNSSEC on a DNS Zone
- [`disableDnsSec()`](core/dnsZones/disableDnsSec.md) — Disable DNSSEC on a DNS Zone
- [`scanRecords()`](core/dnsZones/scanRecords.md) — Trigger a background scan for pre-existing DNS records. Can use ZoneId for existing zones or Domain for pre-zone creation scenarios
- [`latestRecordScan()`](core/dnsZones/latestRecordScan.md) — Get the latest DNS record scan result for a DNS Zone

### `dnsRecords`

DNS records of a zone.

- [`list()`](core/dnsRecords/list.md) — List DNS Zone Records
- [`all()`](core/dnsRecords/all.md) — Iterate lazily over every item of list(), across all pages
- [`create()`](core/dnsRecords/create.md) — Add DNS Record
- [`update()`](core/dnsRecords/update.md) — Update DNS Record
- [`delete()`](core/dnsRecords/delete.md) — Delete DNS Record

### `videoLibraries`

Stream video libraries: settings, API keys, referrer rules, watermarks and statistics. Videos are handled by the Stream API.

- [`list()`](core/videoLibraries/list.md) — List Video Libraries
- [`all()`](core/videoLibraries/all.md) — Iterate lazily over every item of list(), across all pages
- [`get()`](core/videoLibraries/get.md) — Get Video Library
- [`create()`](core/videoLibraries/create.md) — Add Video Library
- [`update()`](core/videoLibraries/update.md) — Update Video Library
- [`delete()`](core/videoLibraries/delete.md) — Delete Video Library
- [`languages()`](core/videoLibraries/languages.md) — Get Languages
- [`resetApiKey()`](core/videoLibraries/resetApiKey.md) — Reset API Key
- [`resetReadOnlyApiKey()`](core/videoLibraries/resetReadOnlyApiKey.md) — Reset Read Only API Key
- [`addAllowedReferrer()`](core/videoLibraries/addAllowedReferrer.md) — Add Allowed Referer
- [`removeAllowedReferrer()`](core/videoLibraries/removeAllowedReferrer.md) — Remove Allowed Referer
- [`addBlockedReferrer()`](core/videoLibraries/addBlockedReferrer.md) — Add Blocked Referer
- [`removeBlockedReferrer()`](core/videoLibraries/removeBlockedReferrer.md) — Remove Blocked Referer
- [`addWatermark()`](core/videoLibraries/addWatermark.md) — Upload the watermark image of a video library
- [`deleteWatermark()`](core/videoLibraries/deleteWatermark.md) — Delete Watermark
- [`addLiveThumbnail()`](core/videoLibraries/addLiveThumbnail.md) — Upload the thumbnail shown for live streams of a video library
- [`deleteLiveThumbnail()`](core/videoLibraries/deleteLiveThumbnail.md) — Delete Live Thumbnail
- [`addLiveWatermark()`](core/videoLibraries/addLiveWatermark.md) — Upload the watermark image of live streams of a video library
- [`deleteLiveWatermark()`](core/videoLibraries/deleteLiveWatermark.md) — Delete Live Watermark
- [`transcribingStatistics()`](core/videoLibraries/transcribingStatistics.md) — Get Video Library Transcribing Statistics
- [`drmStatistics()`](core/videoLibraries/drmStatistics.md) — Get Video Library DRM Statistics

### `statistics`

Account-wide and per-zone traffic statistics.

- [`get()`](core/statistics/get.md) — Get Statistics

### `loadBalancers`

Load balancer statistics and usage.

- [`statistics()`](core/loadBalancers/statistics.md) — Get Load Balancer statistics
- [`attachedPullZones()`](core/loadBalancers/attachedPullZones.md) — Get the pull zones attached to a Load Balancer
- [`accountStatistics()`](core/loadBalancers/accountStatistics.md) — Get statistics roll-ups for all Load Balancers on the account
- [`usage()`](core/loadBalancers/usage.md) — Get current-period request usage and pricing for a Load Balancer

### `billing`

Billing details, summaries, payment requests and invoices.

- [`details()`](core/billing/details.md) — Get Billing Details
- [`summary()`](core/billing/summary.md) — Get Billing Summary
- [`summaryPdf()`](core/billing/summaryPdf.md) — Download the PDF summary of a billing record
- [`paymentRequests()`](core/billing/paymentRequests.md) — Get Pending Payment Requests
- [`paymentRequestInvoicePdf()`](core/billing/paymentRequestInvoicePdf.md) — Download the invoice of a payment request as PDF
- [`affiliate()`](core/billing/affiliate.md) — Get affiliate details

### `apiKeys`

API keys of the account.

- [`list()`](core/apiKeys/list.md) — List API Keys
- [`all()`](core/apiKeys/all.md) — Iterate lazily over every item of list(), across all pages

### `auditLog`

The audit log of changes made to the account.

- [`list()`](core/auditLog/list.md)
- [`all()`](core/auditLog/all.md) — Iterate lazily over every item of list(), across all pages

### `account`

Account-level operations.

- [`close()`](core/account/close.md) — Close the account

### `countries`

The countries known to bunny.net, e.g. for geo blocking.

- [`list()`](core/countries/list.md) — Get Country List

### `regions`

The CDN regions and their pricing.

- [`list()`](core/regions/list.md) — Region list

### `search`

Global search across the resources of the account.

- [`query()`](core/search/query.md) — Global Search

### `pricing`

Price estimations.

- [`estimate()`](core/pricing/estimate.md) — Get active price for a resource and optionally cost estimate if usage amount provided

## Origin Errors API

Client `OriginErrorsClient` in `GoSuccess\Bunny\OriginErrors`, reached via `$bunny->originErrors`.

Requests the CDN could not complete because the origin failed.

- [`get()`](origin-errors/client/get.md) — Get the origin errors of a pull zone on one day

## CDN Logging API

Client `LoggingClient` in `GoSuccess\Bunny\Logging`, reached via `$bunny->logging`.

### `logs`

Raw CDN request logs of the last 3 days.

- [`list()`](logging/logs/list.md) — Query CDN access logs for a pull zone
- [`all()`](logging/logs/all.md) — Iterate lazily over every item of list(), across all pages
- [`legacy()`](logging/logs/legacy.md) — Download the log of one day through the legacy v1 endpoint

## Edge Storage API

Client `StorageClient` in `GoSuccess\Bunny\Storage`, reached via `$storage`.

Files and directories of one storage zone.

- [`list()`](storage/client/list.md) — List the files and directories in a directory
- [`describe()`](storage/client/describe.md) — Get the metadata of a file or directory without downloading it
- [`exists()`](storage/client/exists.md) — Whether a file or directory exists
- [`get()`](storage/client/get.md) — Download a file into memory. For large files, use download()
- [`download()`](storage/client/download.md) — Download a file into a stream, e.g. `Stream::fromFile('backup.zip', 'wb')`
- [`upload()`](storage/client/upload.md) — Upload a file. Missing directories are created; an existing file is replaced
- [`createDirectory()`](storage/client/createDirectory.md) — Create a directory, including missing parents
- [`delete()`](storage/client/delete.md) — Delete a file, or a directory with everything in it when the path ends with a slash
- [`deleteDirectory()`](storage/client/deleteDirectory.md) — Delete a directory with everything in it

## Stream API

Client `StreamClient` in `GoSuccess\Bunny\Stream`, reached via `$stream`.

### `videos`

Videos of the library: uploads, metadata, captions, thumbnails, encoding and playback data.

- [`list()`](stream/videos/list.md) — List Videos
- [`all()`](stream/videos/all.md) — Iterate lazily over every item of list(), across all pages
- [`get()`](stream/videos/get.md) — Get Video
- [`create()`](stream/videos/create.md) — Create Video
- [`update()`](stream/videos/update.md) — Update Video
- [`delete()`](stream/videos/delete.md) — Delete Video
- [`upload()`](stream/videos/upload.md) — Upload the file of a video created with create()
- [`fetch()`](stream/videos/fetch.md) — Create a video from a URL: bunny.net downloads and encodes the file
- [`setThumbnail()`](stream/videos/setThumbnail.md) — Set the thumbnail to an image bunny.net fetches from a URL
- [`useGeneratedThumbnail()`](stream/videos/useGeneratedThumbnail.md) — Use one of the five thumbnails generated while encoding
- [`uploadThumbnail()`](stream/videos/uploadThumbnail.md) — Upload the thumbnail image
- [`addCaption()`](stream/videos/addCaption.md) — Add or replace the captions of one language
- [`deleteCaption()`](stream/videos/deleteCaption.md) — Delete Caption
- [`transcribe()`](stream/videos/transcribe.md) — Transcribe video
- [`smartGenerate()`](stream/videos/smartGenerate.md) — Trigger Smart actions
- [`reencode()`](stream/videos/reencode.md) — Reencode Video
- [`addOutputCodec()`](stream/videos/addOutputCodec.md) — Add output codec to video
- [`repackage()`](stream/videos/repackage.md) — Repackage Video
- [`resolutions()`](stream/videos/resolutions.md) — Video resolutions info
- [`cleanupResolutions()`](stream/videos/cleanupResolutions.md) — Cleanup unconfigured resolutions
- [`storageSize()`](stream/videos/storageSize.md) — Get video storage size info
- [`heatmap()`](stream/videos/heatmap.md) — Get Video Heatmap
- [`playData()`](stream/videos/playData.md) — Get Video play data
- [`playHeatmap()`](stream/videos/playHeatmap.md) — Get the raw heatmap data the player shows on its timeline
- [`oEmbed()`](stream/videos/oEmbed.md) — Get oEmbed data

### `collections`

Collections that group the videos of the library.

- [`list()`](stream/collections/list.md) — Get Collection List
- [`all()`](stream/collections/all.md) — Iterate lazily over every item of list(), across all pages
- [`get()`](stream/collections/get.md) — Get Collection
- [`create()`](stream/collections/create.md) — Create Collection
- [`update()`](stream/collections/update.md) — Update Collection
- [`delete()`](stream/collections/delete.md) — Delete Collection

### `statistics`

View and watch time statistics of the library or of one video.

- [`get()`](stream/statistics/get.md) — Get Video Statistics

## Shield API

Client `ShieldClient` in `GoSuccess\Bunny\Shield`, reached via `$bunny->shield`.

### `zones`

Shield zones: the protection settings of pull zones.

- [`list()`](shield/zones/list.md) — Get all of your Shield Zone Configurations
- [`all()`](shield/zones/all.md) — Iterate lazily over every item of list(), across all pages
- [`get()`](shield/zones/get.md) — Get Singular Shield Zone Configuration
- [`getByPullZone()`](shield/zones/getByPullZone.md) — Get Singular Shield Zone Configuration for PullZone
- [`pullZoneMapping()`](shield/zones/pullZoneMapping.md) — Get Active Shield Zones for Pullzone Mapping
- [`defaults()`](shield/zones/defaults.md) — Get the recommended defaults for creating a Shield Zone
- [`create()`](shield/zones/create.md) — Create a Shield Zone for your PullZone
- [`createUnderAttack()`](shield/zones/createUnderAttack.md) — Create a Shield Zone in under-attack mode for your PullZone
- [`update()`](shield/zones/update.md) — Update your Shield Zone configuration

### `waf`

The managed WAF rules, their review and the WAF settings catalog.

- [`rules()`](shield/waf/rules.md) — Retrieve all available WAF rules for a Shield Zone
- [`rulesByPlan()`](shield/waf/rulesByPlan.md) — Retrieve WAF rules segmented by subscription plan
- [`triggeredRules()`](shield/waf/triggeredRules.md) — Review all triggered WAF rules for the specified Shield Zone
- [`reviewTriggeredRule()`](shield/waf/reviewTriggeredRule.md) — Review and update the action of a triggered WAF rule
- [`recommendation()`](shield/waf/recommendation.md) — Retrieve an AI recommendation for a triggered WAF rule
- [`profiles()`](shield/waf/profiles.md) — Retrieve all available WAF profiles
- [`enums()`](shield/waf/enums.md) — Retrieve all available WAF enum mappings
- [`engineConfig()`](shield/waf/engineConfig.md) — Retrieve the default WAF engine configuration

### `customRules`

Custom WAF rules of Shield zones.

- [`list()`](shield/customRules/list.md) — Retrieve custom WAF rules configured for the specified Shield Zone
- [`all()`](shield/customRules/all.md) — Iterate lazily over every item of list(), across all pages
- [`get()`](shield/customRules/get.md) — Retrieve a specific custom WAF rule
- [`create()`](shield/customRules/create.md) — Create a new custom WAF rule
- [`update()`](shield/customRules/update.md) — Update an existing custom WAF rule
- [`replace()`](shield/customRules/replace.md) — Update an existing custom WAF rule
- [`delete()`](shield/customRules/delete.md) — Delete a custom WAF rule

### `rateLimits`

Rate limit rules of Shield zones.

- [`list()`](shield/rateLimits/list.md) — Get Rate Limits for your Shield Zone
- [`all()`](shield/rateLimits/all.md) — Iterate lazily over every item of list(), across all pages
- [`get()`](shield/rateLimits/get.md) — Get Individual Rate Limit for your Shield Zone
- [`create()`](shield/rateLimits/create.md) — Create a Rate Limit for your Shield Zone
- [`update()`](shield/rateLimits/update.md) — Update a Rate Limit configuration on your Shield Zone
- [`delete()`](shield/rateLimits/delete.md) — Delete a Rate Limit on your Shield Zone

### `accessLists`

Managed and custom access lists of Shield zones.

- [`list()`](shield/accessLists/list.md) — Get all Access Lists available for a Shield Zone
- [`get()`](shield/accessLists/get.md) — Get the specified Custom Access List associated with a Shield Zone
- [`create()`](shield/accessLists/create.md) — Create a new Custom Access List associated with a Shield Zone
- [`update()`](shield/accessLists/update.md) — Update the specified Custom Access List associated with a Shield Zone
- [`delete()`](shield/accessLists/delete.md) — Delete the specified Custom Access List associated with a Shield Zone
- [`configure()`](shield/accessLists/configure.md) — Update Access List Configuration for a Shield Zone
- [`enums()`](shield/accessLists/enums.md) — Get all Access Lists API enumeration types and their values

### `botDetection`

Bot detection and the handling of known bots and bot categories.

- [`get()`](shield/botDetection/get.md) — Your current Bot Detection configuration
- [`update()`](shield/botDetection/update.md) — Update your current Bot Detection configuration
- [`categorization()`](shield/botDetection/categorization.md) — List bots available for explicit allow/block configuration on this Shield Zone, grouped by category
- [`setBotAction()`](shield/botDetection/setBotAction.md) — Set or clear the action applied to a categorised bot for this Shield Zone
- [`setCategoryAction()`](shield/botDetection/setCategoryAction.md) — Set or clear the action applied to every bot in a category for this Shield Zone

### `uploadScanning`

Antivirus and CSAM scanning of uploads.

- [`get()`](shield/uploadScanning/get.md) — Get your Current Upload Scanning Configuration
- [`update()`](shield/uploadScanning/update.md) — Update your Upload Scanning Configuration

### `apiGuardian`

API Guardian: request validation against an OpenAPI specification.

- [`get()`](shield/apiGuardian/get.md) — Get the API Guardian configuration and endpoints
- [`update()`](shield/apiGuardian/update.md) — Update the API Guardian configuration (enabled, execution mode, body limit action)
- [`updateEndpoint()`](shield/apiGuardian/updateEndpoint.md) — Update your API Guardian Endpoint configuration
- [`uploadSpecification()`](shield/apiGuardian/uploadSpecification.md) — Upload your OpenAPI specification
- [`updateSpecification()`](shield/apiGuardian/updateSpecification.md) — Update your OpenAPI specification
- [`enums()`](shield/apiGuardian/enums.md) — Get all API Guardian enumeration types and their values

### `customPages`

Custom HTML pages shown to blocked, challenged or rate-limited visitors.

- [`get()`](shield/customPages/get.md) — Get the HTML of a custom page
- [`upload()`](shield/customPages/upload.md) — Upload the HTML of a custom page, replacing the current one
- [`delete()`](shield/customPages/delete.md) — Delete a custom page; bunny.net's own page is shown again

### `eventLogs`

Event logs of the requests Shield acted on.

- [`list()`](shield/eventLogs/list.md) — Get a page of the event logs of one day
- [`all()`](shield/eventLogs/all.md) — Iterate lazily over all event logs of one day, across all pages
- [`search()`](shield/eventLogs/search.md) — Search, filter and group the event logs of a time window
- [`export()`](shield/eventLogs/export.md) — Export the filtered event logs of a time window as CSV

### `metrics`

Request metrics and billing overages of Shield zones.

- [`overview()`](shield/metrics/overview.md) — Get an overview of metrics for the specified Shield Zone
- [`detailed()`](shield/metrics/detailed.md) — Get a detailed metrics overview for the specified Shield Zone within the selected time range and resolution
- [`overages()`](shield/metrics/overages.md) — Get the overage breakdown for the specified Shield Zone for a given month, segmented by billing plan changes
- [`rateLimits()`](shield/metrics/rateLimits.md) — Get aggregated rate limit metrics for the specified Shield Zone
- [`rateLimit()`](shield/metrics/rateLimit.md) — Get detailed metrics for the specified Rate Limit
- [`wafRule()`](shield/metrics/wafRule.md) — Get metrics for a specific WAF Rule within the specified Shield Zone
- [`botDetection()`](shield/metrics/botDetection.md) — Get bot detection metrics for the specified Shield Zone
- [`uploadScanning()`](shield/metrics/uploadScanning.md) — Get upload scanning metrics for the specified Shield Zone
- [`apiGuardian()`](shield/metrics/apiGuardian.md) — Get API Guardian metrics for the specified Shield Zone
- [`apiGuardianEndpoint()`](shield/metrics/apiGuardianEndpoint.md) — Get metrics for a specific API Guardian endpoint within the specified Shield Zone

### `ddos`

The DDoS settings catalog.

- [`enums()`](shield/ddos/enums.md) — List of all DDoS Enum Mappings

### `promotions`

Shield promotions of the account.

- [`state()`](shield/promotions/state.md) — Get the Shield promotions of the account
