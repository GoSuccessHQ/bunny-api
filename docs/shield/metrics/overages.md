# `$bunny->shield->metrics->overages()`

> Shield API · `GET /shield/metrics/overages/{shieldZoneId}`

Get the overage breakdown for the specified Shield Zone for a given month, segmented by billing plan changes

The API answers 404 for a month without billing data of the zone, e.g. before the zone existed.

## Signature

```php
public function overages(int $shieldZoneId, int $year, int $month): MonthlyOverages
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |
| `$year` | `int` | yes | The calendar year to query. |
| `$month` | `int` | yes | The calendar month (1-12) to query. |

## Returns

`MonthlyOverages`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->overages(shieldZoneId: 123, year: 123, month: 123);
```
