# `$bunny->shield->customRules->all()`

> Shield API · `GET /shield/waf/custom-rules/{shieldZoneId}`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(int $shieldZoneId, int $perPage = 1000): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone associated with the Custom WAF Rules. |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<CustomRule>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->shield->customRules->all(shieldZoneId: 123) as $item) {
    // ...
}
```
