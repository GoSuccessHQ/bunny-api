# `$bunny->shield->metrics->botDetection()`

> Shield API · `GET /shield/metrics/shield-zone/{shieldZoneId}/bot-detection`

Get bot detection metrics for the specified Shield Zone

## Signature

```php
public function botDetection(int $shieldZoneId): BotDetectionMetrics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |

## Returns

`BotDetectionMetrics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->botDetection(shieldZoneId: 123);
```
