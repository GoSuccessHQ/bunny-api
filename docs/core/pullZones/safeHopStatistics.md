# `pullZones->safeHopStatistics()`

> Core Platform API · `GET /pullzone/{pullZoneId}/safehop/statistics`

Get SafeHop Statistics

## Signature

```php
public function safeHopStatistics(
    int $pullZoneId,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
    ?bool $hourly = null,
): SafeHopStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | Id of a Pull Zone |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$hourly` | `bool\|null` | no | (Optional) If true, the statistics data will be returned in hourly groupping. |

## Returns

`SafeHopStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->safeHopStatistics(pullZoneId: 123);
```
