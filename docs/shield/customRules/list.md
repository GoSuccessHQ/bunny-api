# `$bunny->shield->customRules->list()`

> Shield API · `GET /shield/waf/custom-rules/{shieldZoneId}`

Retrieve custom WAF rules configured for the specified Shield Zone

## Signature

```php
public function list(int $shieldZoneId, int $page = 1, int $perPage = 100): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone associated with the Custom WAF Rules. |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Page<CustomRule>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->customRules->list(shieldZoneId: 123);
```
