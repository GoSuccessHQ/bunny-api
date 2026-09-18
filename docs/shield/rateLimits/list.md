# `$bunny->shield->rateLimits->list()`

> Shield API · `GET /shield/rate-limits/{shieldZoneId}`

Get Rate Limits for your Shield Zone

## Signature

```php
public function list(int $shieldZoneId, int $page = 1, int $perPage = 100): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone associated with the Rate Limits |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Page<RateLimitRule>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->rateLimits->list(shieldZoneId: 123);
```
