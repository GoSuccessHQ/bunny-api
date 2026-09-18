# `pullZones->optimizerStatistics()`

> Core Platform API · `GET /pullzone/{pullZoneId}/optimizer/statistics`

Get optimizer statistics

## Signature

```php
public function optimizerStatistics(
    int $pullZoneId,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
    ?bool $hourly = null,
): OptimizerStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | Id of Pull Zone |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$hourly` | `bool\|null` | no | (Optional) If true, the statistics data will be returned in hourly groupping. |

## Returns

`OptimizerStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->optimizerStatistics(pullZoneId: 123);
```
