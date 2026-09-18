# `$bunny->shield->rateLimits->get()`

> Shield API · `GET /shield/rate-limit/{id}`

Get Individual Rate Limit for your Shield Zone

## Signature

```php
public function get(int $id): RateLimitRule
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Rate Limit |

## Returns

`RateLimitRule`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->rateLimits->get(id: 123);
```
