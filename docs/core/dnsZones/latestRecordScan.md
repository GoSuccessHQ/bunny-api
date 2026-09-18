# `$bunny->core->dnsZones->latestRecordScan()`

> Core Platform API · `GET /dnszone/{zoneId}/records/scan`

Get the latest DNS record scan result for a DNS Zone

## Signature

```php
public function latestRecordScan(int $zoneId): DnsRecordScan
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int` | yes | The DNS Zone ID |

## Returns

`DnsRecordScan`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->latestRecordScan(zoneId: 123);
```
