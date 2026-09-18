# `$bunny->core->dnsZones->statistics()`

> Core Platform API · `GET /dnszone/{id}/statistics`

Get DNS Query Statistics

## Signature

```php
public function statistics(
    int $id,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
): DnsZoneStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the DNS Zone for which the statistics will be returned |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 30 days will be returned |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, the last 30 days will be returned |

## Returns

`DnsZoneStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->statistics(id: 123);
```
