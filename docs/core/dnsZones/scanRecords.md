# `$bunny->core->dnsZones->scanRecords()`

> Core Platform API · `POST /dnszone/records/scan`

Trigger a background scan for pre-existing DNS records. Can use ZoneId for existing zones or Domain for pre-zone creation scenarios.

## Signature

```php
public function scanRecords(?int $zoneId = null, ?string $domain = null): DnsRecordScanTrigger
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int\|null` | no | The ID of the DNS Zone to scan. Either ZoneId or Domain must be provided, but not both. |
| `$domain` | `string\|null` | no | The domain name to scan. Either ZoneId or Domain must be provided, but not both. Can be used even before creating the DNS zone. |

## Returns

`DnsRecordScanTrigger`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->scanRecords();
```
