# API Reference

One page per resource method. See the [README](../README.md) for an introduction and [examples/](../examples/) for runnable scripts.

## Core Platform API

Namespace `GoSuccess\Bunny\Core`, client `CoreClient`, reached via `$bunny->core`.

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
