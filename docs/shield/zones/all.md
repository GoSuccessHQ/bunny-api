# `$bunny->shield->zones->all()`

> Shield API · `GET /shield/shield-zones`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(int $perPage = 1000): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<ShieldZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->shield->zones->all() as $item) {
    // ...
}
```
