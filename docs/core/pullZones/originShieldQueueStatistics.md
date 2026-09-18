# `pullZones->originShieldQueueStatistics()`

> Core Platform API · `GET /pullzone/{pullZoneId}/originshield/queuestatistics`

Get Origin Shield Queue Statistics

## Signature

```php
public function originShieldQueueStatistics(
    int $pullZoneId,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
    ?bool $hourly = null,
): OriginShieldConcurrencyStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | Id of a Pull Zone |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$hourly` | `bool\|null` | no | (Optional) If true, the statistics data will be returned in hourly groupping. |

## Returns

`OriginShieldConcurrencyStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->originShieldQueueStatistics(pullZoneId: 123);
```
