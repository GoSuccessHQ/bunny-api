# `$bunny->shield->metrics->rateLimit()`

> Shield API · `GET /shield/metrics/rate-limit/{id}`

Get detailed metrics for the specified Rate Limit

## Signature

```php
public function rateLimit(int $id): RateLimitRuleMetrics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Rate limit. |

## Returns

`RateLimitRuleMetrics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->rateLimit(id: 123);
```
