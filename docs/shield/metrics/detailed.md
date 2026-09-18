# `$bunny->shield->metrics->detailed()`

> Shield API · `GET /shield/metrics/overview/{shieldZoneId}/detailed`

Get a detailed metrics overview for the specified Shield Zone within the selected time range and resolution

## Signature

```php
public function detailed(
    int $shieldZoneId,
    ?DateTimeInterface $startDate = null,
    ?DateTimeInterface $endDate = null,
    ?MetricsOverviewResolution $resolution = null,
): DetailedMetrics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |
| `$startDate` | `DateTimeInterface\|null` | no |  |
| `$endDate` | `DateTimeInterface\|null` | no |  |
| `$resolution` | `MetricsOverviewResolution\|null` | no | 0 = Auto 1 = TwoMinutes 2 = TenMinutes 3 = Hourly 4 = Daily 5 = Weekly 6 = Monthly |

## Returns

`DetailedMetrics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->detailed(shieldZoneId: 123);
```
