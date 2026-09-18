# `$bunny->shield->metrics->overview()`

> Shield API · `GET /shield/metrics/overview/{shieldZoneId}`

Get an overview of metrics for the specified Shield Zone

## Signature

```php
public function overview(int $shieldZoneId): ZoneMetrics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |

## Returns

`ZoneMetrics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->overview(shieldZoneId: 123);
```
