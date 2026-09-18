# `$bunny->shield->metrics->rateLimits()`

> Shield API · `GET /shield/metrics/rate-limits/{shieldZoneId}`

Get aggregated rate limit metrics for the specified Shield Zone

## Signature

```php
public function rateLimits(int $shieldZoneId): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |

## Returns

`list<ZoneRateLimitMetrics>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->rateLimits(shieldZoneId: 123);
```
