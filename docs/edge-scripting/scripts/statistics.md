# `$bunny->edgeScripting->scripts->statistics()`

> Edge Scripting API · `GET /compute/script/{id}/statistics`

Get Edge Script Statistics

## Signature

```php
public function statistics(
    int $id,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
    ?bool $loadLatest = null,
    ?bool $hourly = null,
): EdgeScriptStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Edge Script for which the statistics will be returned |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 30 days will be returned |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, the last 30 days will be returned |
| `$loadLatest` | `bool\|null` | no | (Optional) Load most recent data as soon as it's available |
| `$hourly` | `bool\|null` | no | (Optional) If true, the statistics data will be returned in hourly groupping. |

## Returns

`EdgeScriptStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->scripts->statistics(id: 123);
```
