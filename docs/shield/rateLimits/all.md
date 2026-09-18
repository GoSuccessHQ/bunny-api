# `$bunny->shield->rateLimits->all()`

> Shield API · `GET /shield/rate-limits/{shieldZoneId}`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(int $shieldZoneId, int $perPage = 1000): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone associated with the Rate Limits |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<RateLimitRule>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->shield->rateLimits->all(shieldZoneId: 123) as $item) {
    // ...
}
```
